<?php

namespace Modules\OpenAI\Enums;

enum ChatModelEnum: string
{
/**
*    case GPT_4 = 'gpt-4';
*
*    case GPT_4_0314 = 'gpt-4-0314';
*
*    case GPT_4_32K = 'gpt-4-32k';
*
*    case GPT_4_32K_0314 = 'gpt-4-32k-0314';
*
*    case GPT_3_5_TURBO_0301 = 'gpt-3.5-turbo-0301';
*/

    case GPT_3_5_TURBO = 'gpt-3.5-turbo';

    case GPT_4 = 'gpt-4';



    /**
     * @return String[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
