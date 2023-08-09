<?php

namespace Modules\Export\Requests;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\CompilationExport;

class JSONRequest extends FormRequest
{

    public function rules($id, $imports)
    {
        $export = CompilationExport::get()->where('id', $id)->first()->data;

        $result = array();


        foreach ($export as $key => $item){
            foreach ($item as $lang => $value){
                $result[$key . '_' . $lang] = $value;
            }
        }

        $output = [];
        foreach ($result as $key => $values) {
            foreach ($values as $index => $content) {
                $output[$index][$key] = $content;
            }
        }

        $import = array();
        foreach ($imports as $index => $item){
            foreach (json_decode($item->data) as $key => $value){
                $import[$index][$value->header] = $value->value;
            }
            $import[$index] = [...$import[$index], ...$output[$index]];
        }

        return $import;
    }
}
