<?php

namespace Modules\Compilations\Policies;

use App\Models\User;

class CompilationPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if (!$user->ownsTeam($user->currentTeam) &&
            $user->hasTeamRole($user->currentTeam, 'viewer')) {
            return false;
        }

        return null;
    }

    /**
     * Determine whether the user can manage the model before real actions.
     */
    public function manage(User $user): bool
    {
        return true;
    }
}
