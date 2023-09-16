<?php

namespace Modules\OpenAI\Actions;

use App\Models\User;
use Modules\OpenAI\Contracts\CompletesItemStreamed;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateStreamedResponse;

class CompleteItemStreamed implements CompletesItemStreamed
{
    public function complete(User $user, array $config): void
    {
        if (!$this->validate($user)) {
            echo "event: error\n";
            echo 'data: '.__('openai.limit-reached');
            echo "\n\n";
            echo str_pad('-', 4096)."\n";
            ob_flush();
            flush();

            return;
        }

        $stream = OpenAI::chat()->createStreamed($config);

        // ------------------------------------------------
        // count subscription plan ------------------------
        $user->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
        $user->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
        // ------------------------------------------------

        foreach ($stream as $response) {
            if (connection_aborted()) {
                break;
            }

            echo "event: update\n";
            echo 'data: ' . json_encode($this->formatResponse($response));
            // TODO: temporary workaround
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

    protected function validate(User $user)
    {
        $planSubscription = $user->currentTeam->planSubscription;

        return $planSubscription->canUseFeature(SubscriptionFeatureEnum::OPENAI_REQUESTS) &&
            $planSubscription->canUseFeature(SubscriptionFeatureEnum::API_REQUESTS);
    }
}
