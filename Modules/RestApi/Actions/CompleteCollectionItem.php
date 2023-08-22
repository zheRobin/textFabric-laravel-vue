<?php

namespace Modules\RestApi\Actions;

use App\Models\User;
use DeepL\Translator;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\RestApi\Contracts\CompletesCollectionItem;
use Modules\OpenAI\Services\PromptService;
use Modules\Presets\Models\Preset;
use Modules\Translations\Contracts\TranslatesData;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Modules\Translations\Models\Language;

class CompleteCollectionItem implements CompletesCollectionItem
{
    public function complete(User $user, Preset $preset, CollectionItem $collectionItem, $translate, $sourceList)
    {
        // authorize

        $promptService = app(PromptService::class);
        $promptData = $promptService->getData($collectionItem);
        // translate input
        if ($preset->translateInput()) {
            $arrayTranslator = app(TranslatesData::class);
            $promptData = $arrayTranslator->translate($promptData, 6);
        }

        // build prompt
        $builder = app(BuildsPrompt::class);
        $system_prompt = strtr($preset->system_prompt, $sourceList['system_prompt']);
        $user_prompt = strtr($preset->user_prompt, $sourceList['user_prompt']);

        $systemMessage = $builder->build($promptData, $system_prompt);
        $userMessage = $builder->build($promptData, $user_prompt);

        $params = $preset->getChatParams($systemMessage, $userMessage);
        $completion = OpenAI::chat()->create($params);
        $formattedResponse = $this->formatResponse($completion);

        $result = [];

        $translator = app(Translator::class);
        foreach ($translate as $lang) {
            $translatedText = $translator->translateText($formattedResponse, null, $lang);
            $result[$lang] = $translatedText->text;
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

}
