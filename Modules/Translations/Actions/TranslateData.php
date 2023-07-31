<?php

namespace Modules\Translations\Actions;

use DeepL\Translator;
use Illuminate\Support\Facades\Log;
use Modules\Translations\Contracts\TranslatesData;
use Modules\Translations\Models\Language;

class TranslateData implements TranslatesData
{
    public function translate(array $data, Language $language): array
    {
        if (empty($data)) {
            return $data;
        }

        $translator = app(Translator::class);

        $attributes = array_map(fn ($value) => strval($value), $data);

        return array_combine(
            array_keys($attributes),
            array_column(
                $translator->translateText(array_values($attributes), null, $language->code),
                'text'
            )
        );
    }
}
