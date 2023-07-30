<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\Exports;

class JSONRequest extends FormRequest
{

    public function rules($id)
    {

        $export = Exports::get()->where('id', $id)->first()->value;

        $result = array();
        foreach ($export as $key => $item){
            foreach ($item as $lang => $value){
                $result[$key.'_'.$lang ] = $value;
            }
        }

        return json_encode($result);
    }
}
