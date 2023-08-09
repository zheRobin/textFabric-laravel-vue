<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\CompilationExport;

class CSVRequest extends FormRequest
{
    public function rules($item)
    {
        $dataArray = $item;

        $csvOutput = '';

        $header = array_keys($dataArray[0]);
        $csvOutput .= '"' . implode('","', $header) . '"' . "\n";

        foreach ($dataArray as $row) {
            $csvRow = array_map(function ($value) {
                // Замінити подвійні лапки всередині поля на подвійні подвійні лапки (для екранизації)
                $escapedValue = str_replace('"', '""', $value);
                // Обгортаємо поле у подвійні лапки, якщо воно містить подвійні лапки, кому або переноси строк
                if (strpos($escapedValue, '"') !== false || strpos($escapedValue, ',') !== false || strpos($escapedValue, "\n") !== false || strpos($escapedValue, "\r") !== false) {
                    return '"' . $escapedValue . '"';
                }
                return '"' . $escapedValue . '"';
            }, $row);
            $csvOutput .= implode(',', $csvRow) . "\n";
        }


        return $csvOutput;
    }
}
