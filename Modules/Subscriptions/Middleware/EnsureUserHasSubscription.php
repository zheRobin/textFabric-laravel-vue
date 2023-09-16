<?php

namespace Modules\Subscriptions\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
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
     * @return RedirectResponse|Response|JsonResponse|null
     */
    public function handle(Request $request, Closure $next, string ...$subscriptionPlans)
    {
        if (!in_array($request->user()?->currentTeam?->planSubscription?->plan?->slug, $subscriptionPlans)) {

            if ($request->wantsJson()) {
                $response = [
                    "message" => trans('Your subscription plan does not support it.'),
                    "timestamp" => now()
                ];

                return new JsonResponse($response, 403);
            } else {
                return Redirect::route('profile.show');
            }
        }

        return $next($request);
    }
}
