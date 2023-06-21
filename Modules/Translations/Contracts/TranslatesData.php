<?php

namespace Modules\Translations\Contracts;

use Modules\Translations\Models\Language;

interface TranslatesData
{
    public function translate(array $data, Language $language): array;
}
