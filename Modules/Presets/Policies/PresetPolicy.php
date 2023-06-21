<?php

namespace Modules\Presets\Policies;

use App\Models\User;
use Modules\Collections\Models\Collection;
use Modules\Presets\Models\Preset;

class PresetPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Collection $collection): bool
    {
        return $user->currentTeam->ownsCollection($collection);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Preset $preset, Collection $collection): bool
    {
        return $preset->collection->team->is($user->currentTeam) &&
            $user->currentTeam->ownsCollection($collection);
    }
}
