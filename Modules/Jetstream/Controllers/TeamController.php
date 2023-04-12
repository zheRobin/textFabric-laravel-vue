<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\RedirectsActions;
use Modules\Jetstream\Contracts\TogglesDisabledTeam;

class TeamController extends Controller
{
    use RedirectsActions;

    /**
     * @return Response
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Jetstream::newTeamModel());

        $teams = Team::with(['users', 'owner'])->get();

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
        ]);
    }

    /**
     * @param Request $request
     * @param int $teamId
     * @return RedirectResponse
     */
    public function toggleDisabled(Request $request, int $teamId): RedirectResponse
    {
        Gate::authorize('toggleDisabled', Jetstream::newTeamModel());

        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        $toggler = app(TogglesDisabledTeam::class);

        $toggler->toggle($team, $request->disabled);

        return back();
    }
}
