<?php

namespace Modules\OpenAI\Actions;

use Modules\OpenAI\Contracts\BuildsPrompt;

class BuildPrompt implements BuildsPrompt
{
    public function build(array $data, string $prompt): string
    {
        $headers = array_map(function ($item) {
            return "@($item)";
        }, array_keys($data));

        $replacedPrompt = str_replace($headers, array_values($data), $prompt);

        $replacedPrompt = preg_replace('/(\@\((?:\[??[^\[]*?\)))/m', '', $replacedPrompt);

        return trim(
            preg_replace('/\s+/', ' ', $replacedPrompt)
        );
    }
}
