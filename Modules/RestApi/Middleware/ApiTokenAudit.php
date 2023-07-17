<?php

namespace Modules\RestApi\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Models\Team;
class ApiTokenAudit
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
        if ($request->user()->currentAccessToken()->abilities[0] !== "Enable") {
            throw new HttpException(403, "Forbidden, token is no longer available");
        }

        if (Team::get()->where('user_id', 6)->first()->planSubscription->plan->id !== 2) {
            throw new HttpException(403, "Forbidden, token is no longer available");
        }

        return $next($request);
    }
}
