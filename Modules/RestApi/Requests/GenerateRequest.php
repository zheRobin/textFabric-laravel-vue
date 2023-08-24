<?php

namespace Modules\RestApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Translations\Models\Language;

class GenerateRequest extends FormRequest
{
    public function rules(): array
    {
        $langCodes = Language::where('target', 1)->pluck('code');
        return [
            "generate" => [
                'preset-id'               => ['required', 'numeric', 'exists:presets,id'],
                'source-list'             => ["user_prompt" => ["@name" => "string"], "system_prompt" => ["@name" => "string"] ],
                'translate-target-list'   => ['required', 'array'],
                'translate-target-list.*' => [
                    (string) Rule::in($langCodes),
                ],
            ],
            "translate" => [
                'text' => ['required', 'string'],
                'translate-target-list'   => ['required', 'array'],
                'translate-target-list.*' => [
                    (string) Rule::in($langCodes),
                    ],
                ]
        ];
    }
}
