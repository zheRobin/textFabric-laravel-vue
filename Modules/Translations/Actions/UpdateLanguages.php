<?php

namespace Modules\Translations\Actions;

use DeepL\Translator;
use Modules\Translations\Contracts\UpdatesLanguages;
use Modules\Translations\Models\Language;

class UpdateLanguages implements UpdatesLanguages
{
    public function __construct(
        public Translator $translator
    ) {
    }

    public function update(): void
    {
        foreach ($this->translator->getSourceLanguages() as $language) {
            Language::query()->updateOrCreate([
                'code' => $language->code
            ], [
                'name' => $language->name,
                'source' => true,
            ]);
        }

        foreach ($this->translator->getTargetLanguages() as $language) {
            Language::query()->updateOrCreate([
                'code' => $language->code
            ], [
                'name' => $language->name,
                'target' => true,
                'formality_support' => $language->supportsFormality
            ]);
        }
    }
}
