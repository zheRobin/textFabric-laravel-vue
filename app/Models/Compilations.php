<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compilations extends Model
{
    protected $fillable = ['name', 'owner', 'preset_ids'];
}
