<?php

namespace Modules\RestApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
                "message" => "Access denied",
                "timestamp" => now()
            ];
            return new JsonResponse($response, 401);
        }

        if (count($request->user()->currentAccessToken()->abilities) === 0) {
            $response = [
                "message" => "Access denied",
                "timestamp" => now()
            ];

            return new JsonResponse($response, 403);
        }

        if (Team::where('user_id', $request->user()->id)->first()->planSubscription->plan->id !== 3) {
            $response = [
                "message" => "Access denied",
                "timestamp" => now()
            ];

            return new JsonResponse($response, 403);
        }

        return $next($request);
    }
}
