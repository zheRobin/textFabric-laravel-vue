<?php

namespace Modules\Subscriptions\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $subscriber_type
 * @property int $subscriber_id
 * @property int $plan_id
 * @property string $name
 * @property string $description
 * @property Carbon|null $trial_ends_at
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $cancels_at
 * @property Carbon|null $canceled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class PlanSubscription extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['plan'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_active',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'slug',
        'name',
        'description',
        'starts_at',
        'on_trial',
        'ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'ends_at' => 'datetime',
        'on_trial' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return Attribute
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->active()
        );
    }

    /**
     * @return MorphTo
     */
    public function subscriber(): MorphTo
    {
        return $this->morphTo('subscriber');
    }

    /**
     * @return bool
     */
    public function active(): bool
    {
        return !$this->ended();
    }

    /**
     * @return bool
     */
    public function inActive(): bool
    {
        return !$this->active();
    }

    /**
     * @return bool
     */
    public function ended(): bool
    {
        if ($this->ends_at) {
            return Carbon::now()->gte($this->ends_at);
        }

        return true;
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('ends_at', '>', Carbon::now());
    }

    /**
     * @param Builder $query
     * @return void
     */
    public function scopeInActive(Builder $query): void
    {
        $query->where('ends_at', '<=', Carbon::now());
    }
}
