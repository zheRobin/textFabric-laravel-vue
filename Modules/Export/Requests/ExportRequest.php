<?php

namespace Modules\Export\Requests;

use App\Http\Middleware\RedirectIfAuthenticated;
use http\Env\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Export\Models\Exports;
use Modules\Translations\Models\Language;

class ExportRequest extends FormRequest
{
    public function rules($id): array
    {
        $export = Exports::get()->where('id', $id)->first()->value;
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
