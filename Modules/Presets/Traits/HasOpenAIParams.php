<?php

namespace Modules\Presets\Traits;

use Modules\OpenAI\Enums\ChatRoleEnum;

trait HasOpenAIParams
{
    public function getChatParams(string $message, ChatRoleEnum $role)
    {
        return array_filter([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => $role->roleName(),
                    'content' => $message,
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
