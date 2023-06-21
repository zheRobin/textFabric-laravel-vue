<?php

namespace Modules\Presets\Contracts;

use App\Models\User;
use Modules\Presets\Data\PresetInput;
use Modules\Presets\Models\Preset;

interface UpdatesPreset
{
    public function update(User $user, Preset $preset, PresetInput $presetInput): void;
}
