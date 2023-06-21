<?php

namespace Modules\Presets\Data\Casts;

use Modules\OpenAI\Enums\ChatModelEnum;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\DataProperty;

class ChatModelCast implements Cast
{
    public function cast(DataProperty $property, mixed $value, array $context): ChatModelEnum
    {
        return ChatModelEnum::from($value);
    }
}
