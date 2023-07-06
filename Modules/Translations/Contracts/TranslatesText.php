<?php

namespace Modules\Translations\Contracts;

interface TranslatesText
{
    public function translate(array $input): string;
}
