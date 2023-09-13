<?php

namespace Modules\Export\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Compilations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Export\Enums\ExportTypeEnum;

/**
 * @mixin Builder
 */
class Export extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['batch'];

    protected $fillable = [
        'collection_id',
        'name',
        'type',
        'job_batch_id',
    ];

    protected $casts = [
        'value' => 'array',
        'type' => ExportTypeEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function compilation(): BelongsTo
    {
        return $this->belongsTo(Compilations::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(ExportCollectionItem::class);
    }

    /**
     * @return BelongsTo
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(JobBatch::class, 'job_batch_id', 'id');
    }

    /**
     * @param string $name
     * @return string
     */
    public static function buildName(string $name): string
    {
        return sprintf("%s - %s - %s", $name, Carbon::now()->format('d.m.Y'), Carbon::now()->format('H-i-s'));
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeHistory(Builder $builder): Builder
    {
        return $builder
            ->whereNull('job_batch_id')
            ->whereHas('items');
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeActive(Builder $builder): Builder
    {
        return $builder->whereHas('batch', function (Builder $query) {
            $query->whereNull('finished_at');
        });
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeCancelled(Builder $builder): Builder
    {
        return $builder
            ->where(fn($query) => $query
                ->whereHas('batch', fn($query) => $query->whereNotNull('cancelled_at'))
                ->orWhereDoesntHave('items')
            );
    }
}
