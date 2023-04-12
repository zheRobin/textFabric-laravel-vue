<?php

namespace Modules\Jetstream\Actions;

use App\Models\Team;
use Modules\Jetstream\Contracts\TogglesDisabledTeam;

class ToggleDisabledTeam implements TogglesDisabledTeam
{
    /**
     * @inheritDoc
     */
    public function toggle(Team $team, bool $disabled): void
    {
        $team->forceFill([
            'disabled' => $disabled
        ])->save();
    }
}
