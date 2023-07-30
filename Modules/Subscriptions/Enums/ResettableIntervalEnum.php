<?php

namespace Modules\Subscriptions\Enums;

enum ResettableIntervalEnum: string
{
    case MONTH = 'month';

    /**
     * @return String[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
