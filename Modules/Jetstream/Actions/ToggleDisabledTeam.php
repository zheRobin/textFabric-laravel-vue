<?php

namespace Modules\Jetstream\Actions;

use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Modules\Jetstream\Contracts\TogglesDisabledTeam;

class ToggleDisabledTeam implements TogglesDisabledTeam
{
    /**
     * @inheritDoc
     */
    public function toggle(Team $team, bool $disabled): void
    {
        DB::transaction(function () use ($team, $disabled) {
            $team->forceFill([
                'disabled' => $disabled
            ])->save();

//            if ($disabled) {
//                $this->unpickCurrentTeam($team);
//            }
        });
    }

    /**
     * @param Team $team
     * @return void
     */
    protected function unpickCurrentTeam(Team $team): void
    {
        foreach ($team->allUsers() as $user) {
            if ($user->isCurrentTeam($team)) {
                $user->forceFill([
                    'current_team_id' => null,
                ])->save();
            }
        }
    }
}
