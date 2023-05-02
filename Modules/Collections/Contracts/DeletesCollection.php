<?php

namespace Modules\Collections\Contracts;

use App\Models\User;
use Modules\Collections\Models\Collection;

interface DeletesCollection
{
    /**
     * @param User $user
     * @param Collection $collection
     * @return void
     */
    public function delete(User $user, Collection $collection): void;
}
