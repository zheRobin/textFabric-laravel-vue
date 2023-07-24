<?php

namespace Modules\Jetstream\Enums;

enum AppSettingEnum: string
{
    case LOGO = 'logo';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
