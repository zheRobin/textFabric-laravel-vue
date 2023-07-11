<?php

namespace Modules\Collections\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EnsureUserHasCollection
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
            !$request->user()->currentCollection()->exists()) {
            return $request->wantsJson()
                ? abort(403, 'User has not selected any collection.')
                : Redirect::route('collections.create');
        }

        return $next($request);
    }
}
