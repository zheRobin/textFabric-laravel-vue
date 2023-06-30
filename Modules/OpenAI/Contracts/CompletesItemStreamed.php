<?php

namespace Modules\OpenAI\Contracts;

interface CompletesItemStreamed
{
    public function complete(array $config): void;
}
