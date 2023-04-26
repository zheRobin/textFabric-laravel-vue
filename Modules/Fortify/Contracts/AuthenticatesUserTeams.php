<?php

namespace Modules\Fortify\Contracts;

interface AuthenticatesUserTeams
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next);
}
