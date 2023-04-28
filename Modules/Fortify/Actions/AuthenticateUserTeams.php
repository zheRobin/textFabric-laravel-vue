<?php

namespace Modules\Fortify\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Jetstream\Jetstream;
use Modules\Fortify\Contracts\AuthenticatesUserTeams;

class AuthenticateUserTeams implements AuthenticatesUserTeams
{
    public function __construct(
        protected LoginRateLimiter $limiter,
    ) {
        //
    }

    /**
     * @param $request
     * @param $next
     * @return mixed|void
     */
    public function handle($request, $next)
    {
        $user = Jetstream::userModel()::where(Fortify::username(), $request->email)->first();

        if ($user &&
            !$user->isSuperAdmin() &&
            Hash::check($request->password, $user->password)) {
            $hasEnabledTeam = (bool)$user->allTeams()
                ->where('disabled', 0)
                ->count();

            if (!$hasEnabledTeam) {
                $this->throwFailedAuthenticationException($request);
            }
        }

        return $next($request);
    }

    /**
     * Throw a failed authentication validation exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function throwFailedAuthenticationException($request)
    {
        $this->limiter->increment($request);

        throw ValidationException::withMessages([
            Fortify::username() => ['You don\' have access to any team.'],
        ]);
    }
}
