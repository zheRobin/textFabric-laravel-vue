<?php

namespace Modules\Presets\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\Presets\Contracts\ValidatesPreset;

class ValidatePreset implements ValidatesPreset
{
    public function validate(User $user, array $input, string $errorBag): void
    {
        // TODO: remove or refactor (dummy validator)
        Validator::make($input, [
            'prompt' => ['required', 'string'],
            'model' => [],
            'temperature' => 'nullable|numeric|between:0,1',
            'max_tokens' => 'nullable|integer|between:1,2048',
            'top_p' => 'nullable|numeric|between:0,1',
            'frequency_penalty' => 'nullable|numeric|between:0,2',
            'presence_penalty' => 'nullable|numeric|between:0,2',
            'stop' => 'nullable|array|min:1',
            'best_of' => 'nullable|integer|between:1,20',
            'n' => 'nullable|integer|min:1',
            'logit_bias_hr' => 'nullable|array',
            'logit_bias_hr.*.text' => 'required|string',
            'logit_bias_hr.*.bias' => 'required|int|between:-100,100',
        ])->validateWithBag($errorBag);
    }
}
