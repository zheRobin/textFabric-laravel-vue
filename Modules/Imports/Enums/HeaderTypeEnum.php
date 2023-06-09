<?php

namespace Modules\Imports\Enums;

enum HeaderTypeEnum
{
    case IMAGE;

    case TEXT;

    case KEYWORDS;

    case LIST;

    public function slug(): string
    {
        return match ($this) {
            self::IMAGE => 'image',
            self::TEXT => 'text',
            self::KEYWORDS => 'keywords',
            self::LIST => 'list'
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
