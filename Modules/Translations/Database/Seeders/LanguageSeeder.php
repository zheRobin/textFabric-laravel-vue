<?php

namespace Modules\Translations\Database\Seeders;

use DeepL\Translator;
use Illuminate\Database\Seeder;
use Modules\Translations\Contracts\UpdatesLanguages;

class LanguageSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(UpdatesLanguages $updater): void
    {
        $updater->update();
    }
}
