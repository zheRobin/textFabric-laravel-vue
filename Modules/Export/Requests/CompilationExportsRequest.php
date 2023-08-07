<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\Exports;

class CompilationExportsRequest extends FormRequest
{
    public function rules($items)
    {
        return $items;
    }
}
