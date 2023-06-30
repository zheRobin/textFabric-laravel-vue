<?php

namespace Modules\Presets\Contracts;

use App\Models\User;
use Modules\Presets\Data\PresetInput;
use Modules\Presets\Models\Preset;

interface CreatesPreset
{
    public function create(User $user, PresetInput $presetInput): Preset;
}
