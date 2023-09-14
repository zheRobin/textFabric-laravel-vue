<?php

namespace Modules\RestApi\Actions;

use App\Models\User;
use DeepL\DeepLException;
use DeepL\Translator;
use Illuminate\Http\JsonResponse;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\RestApi\Contracts\CompletesCollectionItem;
use Modules\OpenAI\Services\PromptService;
use Modules\Presets\Models\Preset;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Contracts\TranslatesData;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Modules\Translations\Models\Language;
class CompleteCollectionItem implements CompletesCollectionItem
{
    /**
     * @throws DeepLException
     */
    public function complete(User $user, Preset $preset, CollectionItem $collectionItem, $translate, $sourceList)
    {
            if (!$this->validate($user)) {
                $response = [
                    "message" => "Plan limit exceeded",
                    "timestamp" => now()
                ];
                return new JsonResponse($response, 429);
            }

            $systemMessage = $preset->system_prompt;
            $userMessage = $preset->user_prompt;

            if ($sourceList !== null) {
                foreach ($sourceList as $key => $value) {
                    $systemMessage = str_replace($key, $value, $systemMessage);
                    $userMessage = str_replace($key, $value, $userMessage);
                }
            }

            $params = $preset->getChatParams($systemMessage, $userMessage);

            // Handle OpenAI chat request
            $completion = OpenAI::chat()->create($params);

            // ------------------------------------------------
            // count subscription plan ------------------------
            $user->currentTeam->planSubscription
                ->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
            $user->currentTeam->planSubscription
                ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
            // ------------------------------------------------

            $formattedResponse = $this->formatResponse($completion);

            $result['output'] = $formattedResponse;

            $translator = app(Translator::class);
            if (count($translate) > 0) {
                    foreach ($translate as $lang) {
                        $translatedText = $translator->translateText($formattedResponse, null, $lang);

                        // ------------------------------------------------
                        // count subscription plan ------------------------
                        $user->currentTeam->planSubscription
                            ->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
                        $user->currentTeam->planSubscription
                            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
                        // ------------------------------------------------

                        $result[$lang] = $translatedText->text;
                    }
            }

            return $result;
    }

    protected function formatResponse(CreateResponse $response): string
    {
        $contents = [];

        foreach ($response->choices as $choice) {
            $contents[] = $choice->message->content;
        }

        return implode("\r\n----\r\n", $contents);
    }

    protected function validate(User $user)
    {
        $planSubscription = $user->currentTeam->planSubscription;

        return $planSubscription->canUseFeature(SubscriptionFeatureEnum::OPENAI_REQUESTS);
    }

}
