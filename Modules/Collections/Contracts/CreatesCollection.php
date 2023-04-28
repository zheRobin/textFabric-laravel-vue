<?php

namespace Modules\Collections\Contracts;

use App\Models\User;
use Modules\Collections\Models\Collection;

interface CreatesCollection
{
    /**
     * @param User $user
     * @param array $input
     * @return Collection
     */
    public function create(User $user, array $input): Collection;
}
