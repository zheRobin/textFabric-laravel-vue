<?php

namespace Modules\Presets\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Translations\Models\Language;

trait HasLanguage
{
    public function translateInput(): bool
    {
        return !is_null($this->input_language_id);
    }
    public function translateOutput(): bool
    {
        return !is_null($this->output_language_id);
    }

    public function inputLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'input_language_id');
    }

    public function outputLanguage(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'output_language_id');
    }

}
