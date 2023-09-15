<?php

namespace Modules\Subscriptions\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class EnsureUserHasSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$subscriptionPlans
     * @return RedirectResponse|Response|null
     */
    public function handle(Request $request, Closure $next, string ...$subscriptionPlans)
    {
        if (!in_array($request->user()?->currentTeam?->planSubscription?->plan?->slug, $subscriptionPlans)) {
            return $request->wantsJson()
                ? abort(403, 'Your subscription plan does not support it.')
                : Redirect::route('profile.show');
        }

        return $next($request);
    }
}
