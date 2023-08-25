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
    {$systemMessage = $preset->system_prompt;
        $userMessage = $preset->user_prompt;

        if($sourceList !== null){
            foreach ($sourceList as $key => $value) {
                $systemMessage = str_replace($key, $value, $systemMessage);
                $userMessage = str_replace($key, $value, $userMessage);
            }
        }

        $params = $preset->getChatParams($systemMessage, $userMessage);
        $completion = OpenAI::chat()->create($params);
        $formattedResponse = $this->formatResponse($completion);

        $result['output'] = $formattedResponse;

        $translator = app(Translator::class);
        if(count($translate) > 0){
            foreach ($translate as $lang) {
                $translatedText = $translator->translateText($formattedResponse, null, $lang);
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

}
