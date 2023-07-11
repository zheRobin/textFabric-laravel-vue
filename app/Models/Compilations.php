<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compilations extends Model
{
    protected $fillable = ['name', 'owner'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'preset_ids' => 'array',
    ];
}
