<?php

namespace Modules\Export\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class JobBatch extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'options',
        'failed_job_ids',
    ];

    public function processedJobs(): int
    {
        return $this->total_jobs - $this->pending_jobs;
    }

    public function progress(): int
    {
        return $this->total_jobs > 0 ? round(($this->processedJobs() / $this->total_jobs) * 100) : 0;
    }

    public function finished(): bool
    {
        return ! is_null($this->finished_at);
    }

    public function cancelled(): bool
    {
        return ! is_null($this->cancelled_at);
    }
}
