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
        $count = 0;
        foreach ($export as $key => $item){
            $count ++;
            foreach ($item as $lang => $value){
                foreach ($value as $index => $new){
                    $result[$count][$key.$index.'_'.$lang] = $new;

                }
            }
        }

        return $result;
    }
}
