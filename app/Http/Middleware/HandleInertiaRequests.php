<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;
use Modules\Collections\Models\Collection;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Jetstream\Enums\AppSettingEnum;
use Modules\Jetstream\Services\AppSettings;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'googleRecaptchaSiteKey' => config('services.google_recaptcha.site_key'),
            'planSubscription' => function () use ($user) {
                if ($user) {
                    if (!$user->currentTeam->disabled && $user->currentTeam->planSubscription) {
                        return $user->currentTeam->planSubscription;
                    }
                }

                return null;
            },
            'locale' => function () {
                return  LaravelLocalization::getCurrentLocale();
            },
            'localeAll' => function () {
                return LaravelLocalization::getSupportedLocales();
            },
            'language' => function () {
                if(!file_exists(resource_path('lang/'. app()->getLocale().'/'.app()->getLocale() .'.json'))) {
                    return [];
                }
                return json_decode(file_get_contents(resource_path('lang/'.app()->getLocale() .'/'.app()->getLocale() .'.json')) , true);
            },
            'collections' => function () use ($user) {
                return [
                    'canCreateCollection' => $user &&
                                             Gate::forUser($user)->check('create', Collection::class),
                    'canViewCollection' => $user &&
                                           Gate::forUser($user)->check('manage', Collection::class),
                ];
            },
            'canUseApiFeatures' => function () use ($user) {
                return Gate::forUser($user)->allows('use-api-features');
            },
            'mainLogoPath' => AppSettings::get(AppSettingEnum::LOGO)
                ? Storage::url(AppSettings::get(AppSettingEnum::LOGO))
                : '/texthub-logo.svg',
        ]);
    }
}
