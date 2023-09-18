<?php

namespace Modules\Jetstream\Services;

use Illuminate\Database\Eloquent\Builder;
use Modules\Jetstream\Enums\AppSettingEnum;
use Modules\Jetstream\Models\AppSetting;

/**
 * @mixin Builder
 */
class AppSettings
{
    /**
     * @param AppSettingEnum $setting
     * @return string|null
     */
    public static function get(AppSettingEnum $setting): ?string
    {
        $setting = AppSetting::where('name', $setting->value)->first();

        return $setting?->value;
    }

    /**
     * @param AppSettingEnum $setting
     * @param string $value
     * @return void
     */
    public static function update(AppSettingEnum $setting, string $value)
    {
        AppSetting::query()
            ->updateOrCreate(
                ['value' => $value],
                ['name' => $setting->name]
            );
    }
}
