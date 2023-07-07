<?php

namespace Modules\Translations\Actions;

use DeepL\Translator;
use Illuminate\Support\Facades\Validator;
use Modules\Translations\Contracts\TranslatesText;

class TranslateText implements TranslatesText
{
    public function translate(array $input): string
    {
        Validator::make($input, [
            'content' => ['required', 'string'],
            'languageCode' => ['required', 'exists:languages,code']
        ])->validateWithBag('translateText');

        $translator = app(Translator::class);

        return $translator->translateText($input['content'], null, $input['languageCode']);
    }
}
