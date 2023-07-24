<?php

namespace Modules\Jetstream\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Jetstream\Contracts\UpdatesLogo;
use Modules\Jetstream\Enums\AppSettingEnum;
use Modules\Jetstream\Services\AppSettings;

class UpdateLogo implements UpdatesLogo
{
    public function update(User $user, array $input): void
    {
        Gate::forUser($user)->authorize('update-logo');

        Validator::make($input, [
            'logo' => ['required', 'mimes:jpg,jpeg,png,svg', 'max:1024']
        ])->validateWithBag('updateAppLogo');

        // TODO: remove prev stored logo

        $logoPath = $input['logo']->storeAs(
            // TODO: move disk into config for future s3 swap
            'public',
            implode('.', ['logo', $input['logo']->getClientOriginalExtension()])
        );

        AppSettings::update(
            AppSettingEnum::LOGO,
            $logoPath
        );
    }
}
