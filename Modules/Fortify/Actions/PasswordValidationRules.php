<?php

namespace Modules\Fortify\Actions;

use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * @param bool $confirmed
     * @return array
     */
    protected function passwordRules(bool $confirmed = false): array
    {
        $rules = ['required', 'string', new Password];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }
}
