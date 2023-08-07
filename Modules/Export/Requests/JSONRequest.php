<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\CompilationExport;

class JSONRequest extends FormRequest
{

    public function rules($id)
    {

        $export = CompilationExport::get()->where('id', $id)->first()->data;

        $result = array();
        foreach ($export as $key => $item){
            foreach ($item as $lang => $value){
                $result[$key.'_'.$lang ] = $value;
            }
        }

        return json_encode($result);
    }
}
