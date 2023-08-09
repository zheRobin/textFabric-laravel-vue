<?php

namespace Modules\Export\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueProgress extends Model
{
    use HasFactory;

    protected $table = 'queue_progress';

    protected $fillable = ['job_id', 'status', 'progress'];
}
