<?php

namespace Modules\Presets\Actions;

use App\Models\User;
use Modules\Presets\Contracts\DeletesPreset;
use Modules\Presets\Models\Preset;

class DeletePreset implements DeletesPreset
{

    public function delete(User $user, Preset $preset): void
    {
        // TODO: authorization

        $preset->delete();
    }
}
