<?php

namespace Modules\Export\Contracts;

interface BuildsPrompt
{
    public function build(array $data, string $prompt): string;
}
