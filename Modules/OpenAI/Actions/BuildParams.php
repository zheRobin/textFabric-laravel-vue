<?php

namespace Modules\OpenAI\Actions;

use App\Models\User;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsParams;
use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\OpenAI\Services\PromptService;
use Modules\Presets\Models\Preset;
use Modules\Translations\Contracts\TranslatesData;

class BuildParams implements BuildsParams
{
    public function build(User $user, Preset $preset, CollectionItem $collectionItem): array
    {
        $promptService = app(PromptService::class);
        $promptData = $promptService->getData($collectionItem);

        // translate input
        if ($preset->translateInput()) {
            $arrayTranslator = app(TranslatesData::class);

            $promptData = $arrayTranslator->translate($promptData, $preset->inputLanguage);
        }

        // build prompt
        $builder = app(BuildsPrompt::class);
        $systemMessage = $builder->build($promptData, $preset->getSystemMessage());
        $userMessage = $builder->build($promptData, $preset->getUserMessage());

        return $preset->getChatParams($systemMessage, $userMessage);
    }
}
