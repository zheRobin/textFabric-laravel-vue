<?php

namespace Modules\Export\Models;

use App\Models\Compilations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Collections\Models\Collection;
use Illuminate\Database\Eloquent\Builder;
class CompilationExport extends Model
{
    use HasFactory;

    protected $table = 'compilations_export';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'compilation_id',
        'collection_id',
        'team_id',
        'data',
        'batch_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'json',
    ];

    protected $with = [
        'batch',
        'compilation'
    ];

    /**
     * @return BelongsTo
     */
    public function compilation(): BelongsTo
    {
        return $this->belongsTo(Compilations::class);
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeHistory(Builder $builder): Builder
    {
        return $builder->whereNull('batch_id');
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereNotNull('batch_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(JobBatch::class, 'batch_id', 'id');
    }
}
