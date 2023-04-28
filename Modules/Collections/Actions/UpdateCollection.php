<?php

namespace Modules\Collections\Actions;

use App\Models\User;
use Modules\Collections\Contracts\UpdatesCollection;
use Modules\Collections\Models\Collection;

class UpdateCollection implements UpdatesCollection
{
    /**
     * @param User $user
     * @param Collection $collection
     * @param array $input
     * @return void
     */
    public function update(User $user, Collection $collection, array $input): void
    {
        // authorize

        // validate

        // save
    }
}
