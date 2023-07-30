<?php

namespace Modules\Export\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Controllers\Controller;

class ExcelController extends Controller
{
    public function convertJsonToXlsx()
    {
        $json = '[{
            "name": "John Doe",
            "email": "john@example.com",
            "age": 30
        },{
            "name": "Jane Smith",
            "email": "jane@example.com",
            "age": 28
        },{
            "name": "Bob Johnson",
            "email": "bob@example.com",
            "age": 35
        }]';

        // Decode the JSON into an associative array
        $data = json_decode($json, true);

        // Convert the data into a format suitable for the Excel file
        $excelData = collect($data)->map(function ($row) {
            return [
                'Name' => $row['name'],
                'Email' => $row['email'],
                'Age' => $row['age'],
            ];
        });

        // Define column headings for the Excel file
        $headings = [
            'Name',
            'Email',
            'Age',
        ];

        // Generate the Excel file and store it in memory
        $xlsxData = Excel::download(new class($excelData, $headings) implements FromCollection, WithHeadings {
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

        // Return the XLSX data as a BinaryFileResponse
        return new BinaryFileResponse($xlsxData, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="data.xlsx"',
        ]);
    }
}

