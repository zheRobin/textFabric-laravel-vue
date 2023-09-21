<?php

namespace Modules\Jetstream\Actions;

use App\Models\Team;
use Illuminate\Support\Facades\File;
use Laravel\Jetstream\Contracts\DeletesTeams;

class DeleteTeam implements DeletesTeams
{
    /**
     * Delete the given team.
     */
    public function delete(Team $team): void
    {
        $team->purge();

        $teamDirectory = "storage/team-{$team->id}";
        if (File::exists(public_path($teamDirectory))) {
            File::deleteDirectory(public_path($teamDirectory));
        }
    }
}
