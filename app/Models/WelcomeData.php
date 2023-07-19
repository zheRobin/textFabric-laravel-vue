<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class WelcomeData extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'welcome_data'; // Replace 'welcome_data' with your actual table name
    public $translatable = ['title', 'value', 'link_name'];
    protected $fillable = [
        'block_name',
        'title',
        'value',
        'link_name',
        'link',
    ];
}
