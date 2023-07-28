<?php

namespace Modules\Subscriptions\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanSubscriptionUsage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan_subscription_usage';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subscription_id',
        'feature_id',
        'used',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'used' => 'integer',
        'valid_until' => 'datetime',
    ];

    /**
     * @return bool
     */
    public function expired(): bool
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return Carbon::now()->gte($this->valid_until);
    }
}
