<?php

namespace Modules\Presets\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Collections\Models\Collection;
use Modules\Presets\Contracts\CreatesPreset;
use Modules\Presets\Data\PresetInput;
use Modules\Presets\Models\Preset;

class CreatePreset implements CreatesPreset
{
    public function create(User $user, PresetInput $presetInput): void
    {
        $collection = Collection::find($presetInput->collection_id);

        Gate::forUser($user)->authorize('create', [Preset::class, $collection]);

        Preset::create($presetInput->all());
    }
}
