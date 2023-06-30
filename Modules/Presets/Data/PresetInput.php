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
        #[Required, StringType, Max(60)]
        public string $name,
        #[Enum(ChatModelEnum::class), WithCast(ChatModelCast::class)]
        public ChatModelEnum $model,
        #[Nullable, Numeric]
        public null|int|Optional $input_language_id,
        #[Nullable, Numeric]
        public null|int|Optional $output_language_id,
        #[Nullable, StringType]
        public null|string|Optional $system_prompt = '',
        #[Nullable, StringType]
        public null|string|Optional $user_prompt = '',
        #[Numeric, Between(0,2)]
        public float|Optional $temperature = 1,
        #[Numeric, Between(0,1)] // TODO: check min and max
        public float|Optional $top_p = 1,
        #[Numeric, Between(-2,2)]
        public float|Optional $presence_penalty = 0,
        #[Numeric, Between(-2,2)]
        public float|Optional $frequency_penalty = 0,
    ) {
    }
}
