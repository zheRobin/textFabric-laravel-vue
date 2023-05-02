<?php

namespace Modules\Collections\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
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
        Gate::forUser($user)->authorize('update', $collection);

        Validator::make($input, [
            'name' => ['required', 'string']
        ])->validateWithBag('updateCollection');

        $collection->fill([
            'name' => $input['name']
        ])->save();
    }
}
