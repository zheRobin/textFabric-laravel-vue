<?php

namespace Modules\Imports\Contracts;

use App\Models\User;

interface StoresImportingFile
{
    public function store(User $user, array $input): void;
}
