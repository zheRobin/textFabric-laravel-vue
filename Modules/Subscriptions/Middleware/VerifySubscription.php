<?php

namespace Modules\Subscriptions\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class VerifySubscription
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|Response|JsonResponse|null
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() &&
            $request->user()->isSuperAdmin()) {
            return $next($request);
        }

        if (!$request->user()?->currentTeam?->planSubscription?->planIsActive()) {

            if ($request->wantsJson()) {
                $response = [
                    "message" => trans('Team subscription has expired.'),
                    "timestamp" => now()
                ];

                return new JsonResponse($response, 403);
            }

            return !$request->user()?->currentTeam
                ? Redirect::route('profile.show')
                : Redirect::route('teams.show', $request->user()->currentTeam->id);
        }

        return $next($request);
    }
}
