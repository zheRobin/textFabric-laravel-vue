<?php

namespace Modules\Subscriptions\Models;

use Carbon\Carbon;
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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_active',
        'active_trial',
        'active_invoice',
        'valid_until',
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
        'trial_ends_at',
        'starts_at',
        'ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
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
    protected function validUntil(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->onTrial() ? $this->trial_ends_at : $this->ends_at
        );
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
     * @return Attribute
     */
    protected function activeInvoice(): Attribute
    {
        return Attribute::make(
            get: fn () => !$this->ended()
        );
    }

    /**
     * @return Attribute
     */
    protected function activeTrial(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->onTrial(),
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
        return !$this->ended() || $this->onTrial();
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
     * @return bool
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && Carbon::now()->lt($this->trial_ends_at);
    }
}
