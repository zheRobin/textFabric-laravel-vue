<?php

namespace Modules\Fortify\Contracts;

use App\Models\User;

interface CreatesSuperAdminUser
{
    /**
     * @param array $input
     * @return User
     */
    public function create(array $input): User;
}
