<?php

namespace Modules\OpenAI\Contracts;

use App\Models\User;

interface CompletesItemStreamed
{
    public function complete(User $user, array $config): void;
}
