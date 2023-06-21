<?php

namespace Modules\Presets\Traits;

use Modules\OpenAI\Enums\ChatRoleEnum;

trait HasOpenAIParams
{
    public function getChatParams(string $systemMessage, string $userMessage)
    {
        return array_filter([
            'model' => $this->model,
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
            'temperature' => $this->temperature,
            'top_p' => $this->top_p,
            'n' => $this->n,
            'stop' => $this->stop,
            'max_tokens' => $this->max_tokens,
            'presence_penalty' => $this->presence_penalty,
            'frequency_penalty' => $this->frequency_penalty,
        ]);
    }
}
