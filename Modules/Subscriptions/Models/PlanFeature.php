<?php

namespace Modules\Subscriptions\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Subscriptions\Services\Period;

/**
 * @property int $plan_id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $value
 * @property int $resettable_period
 */
class PlanFeature extends Model
{
    use HasFactory;

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
        'value',
        'resettable_period',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'resettable' => 'boolean'
    ];

    /**
     * @param Carbon $dateFrom
     * @return Carbon
     */
    public function getResetDate(Carbon $dateFrom): Carbon
    {
        $period = new Period($this->resettable_period, $dateFrom ?? now(), $this->resettable_interval);

        return $period->getEndDate();
    }
}
