<?php

namespace Modules\Subscriptions\Middleware;

use App\Models\Team;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;

class VerifyApiSubscription
{
    /**
     * Handle an incoming request.
     *
     * `$request->user()` depends on the model in `tokenable_type` field in `personal_access_token` table
     *
     * `App\Models\Team`: `$request->user()` is an instance of `Team` model
     *
     * @deprecated tokenable_type: App\Models\User
     *
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|Response|JsonResponse|null
     */
    public function handle(Request $request, Closure $next)
    {
        $team = $request->user();

        if (!($team instanceof Team)) {
            return new JsonResponse(trans('Only team-level API token are supported.'), 403);
        }

        if (!$team?->planSubscription?->planIsActive()) {

            if ($request->wantsJson()) {
                $response = [
                    "message" => trans('Team subscription has expired.'),
                    "timestamp" => now()
                ];

                return new JsonResponse($response, 403);
            }

            return Redirect::route('teams.show', $team->id);
        }

        return $next($request);
    }
}
