<?php

namespace Modules\Imports\Contracts;

use App\Models\User;
use Modules\Collections\Models\Collection;

interface UpdatesCollectionHeader
{
    public function update(User $user, Collection $collection, array $input): void;
}
