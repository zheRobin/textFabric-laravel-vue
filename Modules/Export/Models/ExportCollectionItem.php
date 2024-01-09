<?php

namespace Modules\Export\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportCollectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'completions',
        'translations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'json',
        'completions' => 'json',
        'translations' => 'json'
    ];

    /**
     * @return Attribute
     */
    protected function exports(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => [
                ...json_decode($attributes['data']),
                ...json_decode($attributes['completions']),
                ...empty($attributes['translations']) ? [] : json_decode($attributes['translations']),
            ],
        );
    }

    public function dataToExport(): array
    {
        return array_combine(
            array_column($this->exports, 'header'),
            array_column($this->exports, 'value'),
        );
    }
}
