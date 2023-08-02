<?php

namespace Modules\Subscriptions\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

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
    protected $with = ['plan', 'usage'];

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
     * @return HasMany
     */
    public function usage(): HasMany
    {
        return $this->hasMany(PlanSubscriptionUsage::class, 'subscription_id', 'id');
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
     * @param SubscriptionFeatureEnum $featureEnum
     * @return PlanFeature|null
     */
    public function getFeature(SubscriptionFeatureEnum $featureEnum): PlanFeature|null
    {
        return $this->plan->features()->where('slug', $featureEnum->slug())->first();
    }

    /**
     * @param SubscriptionFeatureEnum $featureEnum
     * @return string|null
     */
    public function getFeatureValue(SubscriptionFeatureEnum $featureEnum): ?string
    {
        $feature =  $this->getFeature($featureEnum);

        return $feature->value ?? null;
    }

    /**
     * @param SubscriptionFeatureEnum $feature
     * @return bool
     */
    public function canUseFeature(SubscriptionFeatureEnum $feature): bool
    {
        $feature = $this->getFeature($feature);
        $featureKey = $feature?->getKey();
        $featureValue = $feature->value ?? null;

        $usage = $this->usage()->where('feature_id', $featureKey)->first();

        if ($featureValue === 'true') {
            return true;
        }

        if (is_null($usage) &&
            intval($featureValue) > 0) {
            return true;
        }

        if (!$usage ||
            is_null($featureValue) ||
            $featureValue === '0' ||
            $featureValue === 'false') {
            return false;
        }

        if ($usage->expired()) {
            $usage->forceFill([
                'valid_until' => $feature->getResetDate($usage->valid_until),
                'used' => 0,
            ]);
        }

        return $featureValue - $usage->used > 0;
    }

    public function featureAllowsValue(SubscriptionFeatureEnum $feature, int $value): bool
    {
        $feature = $this->getFeature($feature);
        $featureValue = $feature->value ?? null;

        if ($featureValue === 'true') {
            return true;
        }

        if (is_null($featureValue) ||
            $featureValue === '0' ||
            $featureValue === 'false') {
            return false;
        }

        return intval($featureValue) - $value >= 0;
    }

    /**
     * @param SubscriptionFeatureEnum $feature
     * @param int $uses
     * @param bool $incremental
     * @return PlanSubscriptionUsage
     */
    public function recordFeatureUsage(SubscriptionFeatureEnum $feature, int $uses = 1, bool $incremental = true): PlanSubscriptionUsage
    {
        $feature = $this->getFeature($feature);

        $usage = $this->usage()->firstOrNew([
            'subscription_id' => $this->getKey(),
            'feature_id' => $feature->getKey(),
        ]);

        // TODO: Add possible resettable period
        if ($feature->resettable_interval) {
            if (is_null($usage->valid_until)) {
                $usage->valid_until = $feature->getResetDate($this->created_at);
            } elseif ($usage->expired()) {
                $usage->valid_until = $feature->getResetDate($usage->valid_until);
                $usage->used = 0;
            }
        }

        $usage->used = $incremental
            ? $usage->used + $uses
            : $uses;

        $usage->save();

        return $usage;
    }

    /**
     * @param SubscriptionFeatureEnum $feature
     * @param int $uses
     * @return PlanSubscriptionUsage|null
     */
    public function reduceFeatureUsage(SubscriptionFeatureEnum $feature, int $uses = 1): ?PlanSubscriptionUsage
    {
        $feature = $this->getFeature($feature);
        $featureKey = $feature->getKey() ?? null;

        $usage = $this->usage()->where('feature_id', $featureKey)->first();

        if (is_null($usage)) {
            return null;
        }

        $usage->used = max($usage->used - $uses, 0);

        $usage->save();

        return $usage;
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
