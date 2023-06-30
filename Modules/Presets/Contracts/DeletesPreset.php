<?php

namespace Modules\Presets\Contracts;

use App\Models\User;
use Modules\Presets\Models\Preset;

interface DeletesPreset
{
    public function delete(User $user, Preset $preset): void;
}
