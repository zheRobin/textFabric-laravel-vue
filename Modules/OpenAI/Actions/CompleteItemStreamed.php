<?php

namespace Modules\OpenAI\Actions;

use Modules\OpenAI\Contracts\CompletesItemStreamed;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateStreamedResponse;

class CompleteItemStreamed implements CompletesItemStreamed
{
    public function complete(array $config): void
    {
        $stream = OpenAI::chat()->createStreamed($config);

        foreach ($stream as $response) {
            if (connection_aborted()) {
                break;
            }

            echo "event: update\n";
            echo 'data: ' . json_encode($this->formatResponse($response));
            echo "\n\n";

            ob_flush();
            flush();
        }

        echo "event: update\n";
        echo 'data: <END_STREAMING_SSE>';
        echo "\n\n";
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
