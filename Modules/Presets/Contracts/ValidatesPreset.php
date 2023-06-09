<?php

namespace Modules\Presets\Contracts;

use App\Models\User;

interface ValidatesPreset
{
    public function validate(User $user, array $input, string $errorBag): void;
}
