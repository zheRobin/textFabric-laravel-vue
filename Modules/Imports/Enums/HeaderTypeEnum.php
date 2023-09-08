<?php

namespace Modules\Imports\Enums;

enum HeaderTypeEnum
{
    case TITLE;

    case TEXT;

    public function slug(): string
    {
        return match ($this) {
            self::TITLE => 'title',
            self::TEXT => 'text'
        };
    }

    /**
     * @return String[]
     */
    public static function values(): array
    {
        return array_map(fn ($el) => $el->slug(), self::cases());
    }
}
