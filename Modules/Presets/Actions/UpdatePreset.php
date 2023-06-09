<?php

namespace Modules\Presets\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Collections\Models\Collection;
use Modules\Presets\Contracts\UpdatesPreset;
use Modules\Presets\Data\PresetInput;
use Modules\Presets\Models\Preset;

class UpdatePreset implements UpdatesPreset
{
    public function update(User $user, Preset $preset, PresetInput $presetInput): void
    {
        $collection = Collection::find($presetInput->collection_id);

        Gate::forUser($user)->authorize('update', $collection);

        $preset->update($presetInput->all());
    }
}
