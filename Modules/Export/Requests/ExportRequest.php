<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\CompilationExport;

class ExportRequest extends FormRequest
{
    public function rules($id, $imports): array
    {
        $export = CompilationExport::get()->where('id', $id)->first()->data;
        $result = [];
        foreach ($export as $key => $item){
            foreach ($item as $lang => $value){
                $result[$key.'_'.$lang] = $value;
            }
        }
        $import = array();
        foreach ($imports as $index => $item){
            foreach (json_decode($item->data) as $key => $value){
                $import[$index][$value->header] = $value->value;
            }
        }

        $newData = [];
        foreach ($import as $item) {
            foreach ($item as $key => $value) {
                $newData[$key][] = $value;
            }
        }

        return[
            [...$newData ,...$result],
        ];
    }
}
