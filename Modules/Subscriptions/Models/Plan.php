<?php

namespace Modules\Subscriptions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;

/**
 *
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property boolean $is_active
 * @property int $trial_period
 * @property int $invoice_period
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name',
        'description',
        'is_active',
        'trial_period',
        'invoice_period',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['features'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * @return HasMany
     */
    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    /**
     * @return bool
     */
    public function hasTrial(): bool
    {
        return $this->trial_period;
    }
}
