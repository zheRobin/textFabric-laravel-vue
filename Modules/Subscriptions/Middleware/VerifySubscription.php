<?php

namespace Modules\Subscriptions\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class VerifySubscription
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() ||
            ! $request->user()->currentTeam ||
            ! $request->user()->currentTeam->planSubscription ||
            $request->user()->currentTeam->planSubscription->inActive()) {
            return $request->wantsJson()
                ? abort(403, 'Team subscription has expired')
                : Redirect::route('profile.show'); // TODO: flash message
        }

        return $next($request);
    }
}
