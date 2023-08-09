<?php

namespace Modules\OpenAI\Actions;

use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateStreamedResponse;

class CompleteDemoItemStreamed
{
    public function complete(array $config): void
    {
        foreach (OpenAI::chat()->createStreamed($config) as $response) {
            if (connection_aborted()) {
                break;
            }

            echo "event: update\n";
            echo 'data: ' . json_encode($this->formatResponse($response));
            echo "\n\n";
            echo str_pad('', 4096)."\n";

            ob_flush();
            flush();
        }

        echo "event: update\n";
        echo 'data: <END_STREAMING_SSE>';
        echo "\n\n";
        echo str_pad('-', 4096)."\n";
        ob_flush();
        flush();
    }

    protected function formatResponse(CreateStreamedResponse $response): array
    {
        $firstChoice = $response->choices[0]->toArray();

        return [
            'content' => $firstChoice['delta']['content'] ?? '',
        ];
    }

}
