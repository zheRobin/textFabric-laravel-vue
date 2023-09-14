<?php

namespace Modules\Export\Actions;

use App\Models\User;
use DeepL\DeepLException;
use DeepL\Translator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\CircularDependencyException;
use Modules\Imports\Models\CollectionItem;
use Modules\Export\Contracts\BuildsPrompt;
use Modules\Export\Contracts\CompletesCollectionItem;
use Modules\Export\Services\PromptService;
use Modules\Presets\Models\Preset;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Contracts\TranslatesData;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class CompleteCollectionItem implements CompletesCollectionItem
{
    /**
     * @throws DeepLException
     */
    public function complete(User $user, Preset $preset, CollectionItem $collectionItem): string
    {
        // authorize

        $promptService = app(PromptService::class);
        $promptData = $promptService->getData($collectionItem);

        // translate input
        if ($preset->translateInput()) {
            $arrayTranslator = app(TranslatesData::class);

            $promptData = $arrayTranslator->translate($promptData, $preset->inputLanguage);
        }

        // build prompt
        $builder = app(BuildsPrompt::class);
        $systemMessage = $builder->build($promptData, $preset->system_prompt);
        $userMessage = $builder->build($promptData, $preset->user_prompt);

        $params = $preset->getChatParams($systemMessage, $userMessage);
        $completion = OpenAI::chat()->create($params);

        // ------------------------------------------------
        // count subscription plan ------------------------
        $user->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
        $user->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
        // ------------------------------------------------

        $formattedResponse = $this->formatResponse($completion);

        // translate output
        if ($preset->translateOutput()) {
            $translator = app(Translator::class);

            $formattedResponse = $translator->translateText($formattedResponse, null, $preset->outputLanguage->code);

            // ------------------------------------------------
            // count subscription plan ------------------------
            $user->currentTeam->planSubscription
                ->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
            $user->currentTeam->planSubscription
                ->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
            // ------------------------------------------------
        }

        return $formattedResponse;
    }

    protected function formatResponse(CreateResponse $response): string
    {
        $contents = [];

        foreach ($response->choices as $choice) {
            $contents[] = $choice->message->content;
        }

        return implode("\r\n----\r\n", $contents);
    }

}
