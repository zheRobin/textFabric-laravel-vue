<?php

namespace Modules\OpenAI\Actions;

use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\OpenAI\Enums\ChatRoleEnum;

class BuildDemoParams
{
    public function build(array $data, string $system_prompt, string $user_prompt): array
    {
        $items = $this->transformData($data);

        $promptBuilder = app(BuildsPrompt::class);

        $systemMessage = $promptBuilder->build($items, $system_prompt);
        $userMessage = $promptBuilder->build($items, $user_prompt);

        return [
            ...config('openai.default-params'),
            'messages' => [
                [
                    'role' => ChatRoleEnum::SYSTEM->roleName(),
                    'content' => $systemMessage,
                ],
                [
                    'role' => ChatRoleEnum::USER->roleName(),
                    'content' => $userMessage,
                ]
            ],
        ];
    }

    protected function transformData(array $data): array
    {
        $transformed = [];

        foreach ($data as $item) {
            if (array_key_exists('value', $item) &&
                array_key_exists('header', $item)) {
                $transformed[$item['header']] = $item['value'];
            }
        }

        return $transformed;
    }

}
