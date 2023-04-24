<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\UpdatesTeamNames;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\RedirectsActions;
use Modules\Jetstream\Contracts\TogglesDisabledTeam;
use Modules\Jetstream\Services\TeamsFilterService;
use Modules\Subscriptions\Models\Plan;

class TeamController extends Controller
{
    use RedirectsActions;

    /**
     * @return Response
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Jetstream::newTeamModel());

        $teamsFilterer = app(TeamsFilterService::class);

        $filters = $request->offsetGet('filters') ?? [];

        $teams = $teamsFilterer->apply($filters);

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
            'filters' => $filters,
            'subscriptionPlans' => Plan::all(),
        ]);
    }

    /**
     * Show the team management screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Inertia\Response
     */
    public function show(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        Gate::authorize('view', $team);

        return Jetstream::inertia()->render($request, 'Teams/Show', [
            'team' => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'subscriptionPlans' => Plan::query()->get(),
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
                'canToggleDisabled' => Gate::check('toggleDisabled', Jetstream::newTeamModel()),
            ],
        ]);
    }

    /**
     * Show the team creation screen.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Jetstream::newTeamModel());

        return Jetstream::inertia()->render($request, 'Teams/Create');
    }

    /**
     * Create a new team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $creator = app(CreatesTeams::class);

        $creator->create($request->user(), $request->all());

        return $this->redirectPath($creator);
    }

    /**
     * Update the given team's name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(UpdatesTeamNames::class)->update($request->user(), $team, $request->all());

        return back(303);
    }

    /**
     * Delete the given team.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $teamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $teamId)
    {
        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        app(ValidateTeamDeletion::class)->validate($request->user(), $team);

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);

        return $this->redirectPath($deleter);
    }

    /**
     * @param Request $request
     * @param int $teamId
     * @return RedirectResponse
     */
    public function toggleDisabled(Request $request, int $teamId): RedirectResponse
    {
        // TODO: move gates to actions
        Gate::authorize('toggleDisabled', Jetstream::newTeamModel());

        $team = Jetstream::newTeamModel()->findOrFail($teamId);

        $toggler = app(TogglesDisabledTeam::class);

        $toggler->toggle($team, $request->disabled);

        return back(303);
    }
}
