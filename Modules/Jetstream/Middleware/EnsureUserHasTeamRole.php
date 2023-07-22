<?php

namespace Modules\Jetstream\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;


class EnsureUserHasTeamRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return RedirectResponse|Response|null
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        foreach ($roles as $role) {
            if ($request->user() &&
                $request->user()->currentTeam &&
                $request->user()->hasTeamRole($request->user()->currentTeam, $role)) {
                return $next($request);
            }
        }

        return $request->wantsJson()
            ? abort(403, 'Your team role does not support it.')
            : Redirect::route('profile.show');
    }
}
