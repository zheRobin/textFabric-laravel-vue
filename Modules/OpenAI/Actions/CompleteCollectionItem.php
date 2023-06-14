<?php

namespace Modules\OpenAI\Actions;

use App\Models\User;
use DeepL\Translator;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\OpenAI\Contracts\CompletesCollectionItem;
use Modules\OpenAI\Enums\ChatRoleEnum;
use Modules\OpenAI\Services\PromptService;
use Modules\Presets\Models\Preset;
use Modules\Translations\Contracts\TranslatesData;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class CompleteCollectionItem implements CompletesCollectionItem
{
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
        $message = $builder->build($promptData, $preset->prompt_pattern);

        $params = $preset->getChatParams($message, ChatRoleEnum::USER);
        $completion = OpenAI::chat()->create($params);

        $formattedResponse = $this->formatResponse($completion);

        // translate output
        if ($preset->translateOutput()) {
            $translator = app(Translator::class);

            $formattedResponse = $translator->translateText($formattedResponse, null, $preset->outputLanguage->code);
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
