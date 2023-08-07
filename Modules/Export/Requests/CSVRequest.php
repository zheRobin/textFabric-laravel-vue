<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CSVRequest extends FormRequest
{
    public function rules($item)
    {
        // Step 1: Parse JSON string into an array
        $data = json_decode($item, true);
        $csvData = [];

// Combine the key and values for each pair into a single row
        foreach ($data as $key => $values) {
            $row = [$key];
            foreach ($values as $value) {
                $row[] = '"' . str_replace('"', '""', $value) . '"';
            }
            $csvData[] = $row;
        }

// Convert CSV data to a string
        $csvString = '';
        foreach ($csvData as $row) {
            $csvString .= implode(',', $row) . "\n";
        }

        return $csvString;
    }
}
