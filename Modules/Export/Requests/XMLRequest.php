<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\CompilationExport;

class XMLRequest extends FormRequest
{
    public function rules($id): array
    {
        $export = CompilationExport::get()->where('id', $id)->first()->data;
        $result = [];

        foreach ($export as $key => $item){
            foreach ($item as $lang => $value){
                    $result[$key.'_'.$lang] = $value;
            }
        }

        return[
            $result,
        ];
    }
}
