<?php

namespace Modules\Presets\Data;

use Modules\OpenAI\Enums\ChatModelEnum;
use Modules\Presets\Data\Casts\ChatModelCast;
use Spatie\LaravelData\Attributes\Validation\ArrayType;
use Spatie\LaravelData\Attributes\Validation\Between;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Symfony\Contracts\Service\Attribute\Required;

class PresetInput extends Data
{
    public function __construct(
        #[Exists('collections', 'id')]
        public int $collection_id,
        #[Required, StringType, Max(255)]
        public string $name,
        #[Required, StringType]
        public string $system_prompt,
        #[Required, StringType]
        public string $user_prompt,
        #[Enum(ChatModelEnum::class), WithCast(ChatModelCast::class)]
        public ChatModelEnum $model,
        #[StringType]
        public string|Optional $user,
        #[Numeric, Between(0,2)]
        public float|Optional $temperature = 1,
        #[Numeric, Between(0,1)] // TODO: check min and max
        public float|Optional $top_p = 1,
        #[Numeric, Min(1)]
        public int|Optional $n = 1,
        #[Numeric, Between(-2,2)]
        public float|Optional $presence_penalty = 0,
        #[Numeric, Between(-2,2)]
        public float|Optional $frequency_penalty = 0,
        #[Nullable, ArrayType, Min(1)]
        public null|array|Optional $stop = null,
        #[Numeric, Between(1,2048)]
        public int|Optional $max_tokens = 1,
    ) {
    }
}
