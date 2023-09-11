<?php

namespace Modules\Subscriptions\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class VerifySubscription
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
        if ($request->user() &&
            $request->user()->isSuperAdmin()) {
            return $next($request);
        }

        if (! $request->user() ||
            ! $request->user()->currentTeam ||
            ! $request->user()->currentTeam->planSubscription ||
            $request->user()->currentTeam->planSubscription->inactive()) {
            return $request->wantsJson()
                ? abort(403, 'Team subscription has expired.')
                : Redirect::route('profile.show');
        }

        return $next($request);
    }
}
