<?php

namespace Modules\OpenAI\Enums;

enum ChatRoleEnum
{
    case SYSTEM;

    case USER;

    case ASSISTANT;

    public function roleName(): string
    {
        return match ($this) {
            self::SYSTEM => 'system',
            self::USER => 'user',
            self::ASSISTANT => 'assistant'
        };
    }

    /**
     * @return String[]
     */
    public static function roleValues(): array
    {
        return array_map(fn ($el) => $el->roleName(), self::cases());
    }
}
