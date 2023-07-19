<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Dashboard extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'dashboard';
    public $translatable = ['title', 'value', 'link_name', 'link'];

}
