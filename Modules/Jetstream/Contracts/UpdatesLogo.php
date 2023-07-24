<?php

namespace Modules\Jetstream\Contracts;

use App\Models\User;

interface UpdatesLogo
{
    public function update(User $user, array $input): void;
}
