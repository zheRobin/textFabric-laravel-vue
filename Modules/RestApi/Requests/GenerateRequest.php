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
                'headers'                 => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                'method'                  => 'POST',
                'preset-id'               => ['required', 'numeric', 'exists:presets,id'],
                'source-list'             => ['array', "string"],
                'translate-target-list'   => ['array'],
                'translate-target-list.*' => [
                    (string) Rule::in($langCodes),
                ],
            ],
            "translate" => [
                'headers'                  => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
                'method'                   => 'POST',
                'text'                     => ['required', 'string'],
                'translate-target-list'    => ['required', 'array'],
                'translate-target-list.*'  => [
                    (string) Rule::in($langCodes),
                    ],
                ]
        ];
    }
}
