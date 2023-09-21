<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Presets\Models\Preset;

/**
 * @mixin Builder
 */
class Compilations extends Model
{
    protected $fillable = ['name', 'owner', 'preset_ids', 'collection_id'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'preset_ids' => 'array',
    ];

    /**
     * The attributes with default values.
     *
     * @var array
     */
    protected $attributes = [
        'collection_id' => null, // Replace 1 with the default value you want to set.
    ];

    /**
     * @return BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(\Modules\Collections\Models\Collection::class);
    }

    /**
     * @return Collection|array
     */
    public function getPresets(): Collection|array
    {
        return Preset::query()->whereIn('id', $this->preset_ids)->get();
    }
}
