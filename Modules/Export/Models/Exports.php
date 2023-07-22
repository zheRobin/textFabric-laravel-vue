<?php

namespace Modules\Export\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Exports extends Model
{
    use HasFactory;

    protected $table = 'exports'; // Replace 'welcome_data' with your actual table name

    protected $fillable = [
        'name',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
