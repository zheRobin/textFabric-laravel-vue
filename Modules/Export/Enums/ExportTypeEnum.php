<?php

namespace Modules\Export\Enums;

enum ExportTypeEnum: string
{
    case TRANSLATION = 'translation';

    case COMPILATION = 'compilation';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
