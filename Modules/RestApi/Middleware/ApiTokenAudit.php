<?php

namespace Modules\RestApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
class ApiTokenAudit
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            $response = [
                "message" => trans("Access denied."),
                "timestamp" => now()
            ];
            return new JsonResponse($response, 401);
        }

        if (count($request->user()->currentAccessToken()->abilities) === 0) {
            $response = [
                "message" => trans("API token is disabled. Please enable API token to make a request."),
                "timestamp" => now()
            ];

            return new JsonResponse($response, 403);
        }

        if (!in_array(Team::where('user_id', $request->user()->id)->first()->planSubscription->plan->slug, [
            SubscriptionPlanEnum::ENTERPRISE->slug(),
            SubscriptionPlanEnum::UNLIMITED->slug()
        ])) {
            $response = [
                "message" => trans("API access is not available in your subscription plan. Please upgrade to Enterprise or Unlimited plan."),
                "timestamp" => now()
            ];

            return new JsonResponse($response, 403);
        }

        return $next($request);
    }
}
