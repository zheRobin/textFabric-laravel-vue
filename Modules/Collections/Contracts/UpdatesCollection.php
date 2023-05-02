<?php

namespace Modules\Collections\Contracts;

use App\Models\User;
use Modules\Collections\Models\Collection;

interface UpdatesCollection
{
    /**
     * @param User $user
     * @param Collection $collection
     * @param array $input
     * @return void
     */
    public function update(User $user, Collection $collection, array $input): void;
}
