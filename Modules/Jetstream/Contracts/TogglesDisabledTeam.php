<?php

namespace Modules\Jetstream\Contracts;

use App\Models\Team;

interface TogglesDisabledTeam
{
    /**
     * @param Team $team
     * @return void
     */
    public function toggle(Team $team, bool $disabled): void;
}
