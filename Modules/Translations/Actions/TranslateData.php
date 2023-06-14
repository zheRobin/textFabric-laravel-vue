<?php

namespace Modules\Translations\Actions;

use DeepL\Translator;
use Modules\Translations\Contracts\TranslatesData;
use Modules\Translations\Models\Language;

class TranslateData implements TranslatesData
{
    public function translate(array $data, Language $language): array
    {
        $translator = app(Translator::class);

        return array_combine(
            array_keys($data),
            array_column(
                $translator->translateText(array_values($data), null, $language->code),
                'text'
            )
        );
    }
}
