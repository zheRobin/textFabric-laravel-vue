<?php

namespace Modules\OpenAI\Actions;

use App\Models\User;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsPrompt;
use Modules\OpenAI\Contracts\CompletesCollectionItem;
use Modules\OpenAI\Services\PromptService;
use Modules\Presets\Models\Preset;
use Modules\Translations\Contracts\TranslatesData;

class CompleteCollectionItem implements CompletesCollectionItem
{
    public function complete(User $user, Preset $preset, CollectionItem $collectionItem): string
    {
        // authorize

        $promptService = app(PromptService::class);

        $promptData = $promptService->getData($collectionItem);

        // translate input
        if ($preset->translateInput()) {
            $translator = app(TranslatesData::class);

            $promptData = $translator->translate($promptData, $preset->inputLanguage);
        }

        // build prompt
        $builder = app(BuildsPrompt::class);

        $message = $builder->build($promptData, $preset->prompt_pattern);

        // translate output

        return $message;
    }
}
