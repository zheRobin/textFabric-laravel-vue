<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;

class XLSXRequest extends FormRequest
{
    public function convertJsonToXlsx($json)
    {
        // Decode the JSON into an associative array
        $data = json_decode($json, true);

        // Convert the data into a format suitable for the Excel file
        $excelData = collect($data)->map(function ($messages, $key) {
            $messageCount = count($messages);

            $rowData = [
                'Key' => $key,
            ];

            for ($i = 0; $i < $messageCount; $i++) {
                $rowData['Message ' . ($i + 1)] = $messages[$i];
            }

            return $rowData;
        });

        // Define column headings for the Excel file
        $headings = array_merge(['Key'], array_map(function ($index) {
            return 'Message ' . ($index + 1);
        }, range(0, $excelData->max(function ($item) {
            return count($item) - 1;
        }))));

        // Generate the Excel file and store it in memory
        $xlsxFile = Excel::download(new class($excelData, $headings) implements FromCollection, WithHeadings {
            private $data;
            private $headings;

            public function __construct($data, $headings)
            {
                $this->data = $data;
                $this->headings = $headings;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return $this->headings;
            }
        }, 'data.xlsx');
        // Return the XLSX data as a downloadable response
        return $xlsxFile->getFile()->getPathname();
    }
}
