<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompilationExportsRequest extends FormRequest
{
    public function rules($items)
    {
        return $items;
    }
}
