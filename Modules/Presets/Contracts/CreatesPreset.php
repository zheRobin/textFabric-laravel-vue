<?php

namespace Modules\Presets\Contracts;

use App\Models\User;
use Modules\Presets\Data\PresetInput;

interface CreatesPreset
{
    public function create(User $user, PresetInput $presetInput): void;
}
