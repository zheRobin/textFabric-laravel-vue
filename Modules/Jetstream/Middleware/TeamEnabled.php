<?php

namespace Modules\Jetstream\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TeamEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|null
     */
    public function handle(Request $request, Closure $next)
    {
        // skip verification if user is super admin
        if ($request->user() &&
            $request->user()->isSuperAdmin()) {
            return $next($request);
        }

        // check if the team is disabled
        if (! $request->user() ||
            ! $request->user()->currentTeam ||
            $request->user()->currentTeam->disabled) {
            return $request->wantsJson()
                ? abort(403, 'Team has been banned')
                : Redirect::route('teams.show', $request->user()->currentTeam->id);
        }

        return $next($request);
    }
}
