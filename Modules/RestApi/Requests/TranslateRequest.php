<?php

namespace Modules\RestApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Translations\Models\Language;

class TranslateRequest extends FormRequest
{
    public function rules(): array
    {
        $langCodes = Language::pluck('code')->toArray();

        return [
            'text' => ['required', 'string'],
            'translate-target-list'   => ['required', 'array'],
            'translate-target-list.*' => [
                (string) Rule::in($langCodes),
            ],
        ];
    }
}
