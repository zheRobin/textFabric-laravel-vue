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
            'presence_penalty' => $this->presence_penalty,
            'frequency_penalty' => $this->frequency_penalty,
        ]);
    }

    public function getSystemMessage()
    {
        return strval($this->system_prompt);
    }

    public function getUserMessage()
    {
        return strval($this->user_prompt);
    }
}
