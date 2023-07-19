<?php

namespace Modules\Subscriptions\Middleware;

use Illuminate\Http\Request;
use \Closure;
use Illuminate\Support\Facades\Redirect;

class EnsureUserHasSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|null
     */
    public function handle(Request $request, Closure $next, string $subscriptionPlan)
    {
        if (! $request->user() ||
            ! $request->user()->currentTeam ||
            ! $request->user()->currentTeam->planSubscription ||
            $request->user()->currentTeam->planSubscription->plan->slug !== $subscriptionPlan) {
            return $request->wantsJson()
                ? abort(403, 'Your subscription plan does not support it.')
                : Redirect::route('profile.show');
        }

        return $next($request);
    }
}
