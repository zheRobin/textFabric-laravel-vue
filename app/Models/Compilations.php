<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
