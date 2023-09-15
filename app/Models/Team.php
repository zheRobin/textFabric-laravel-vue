<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Modules\Collections\Models\Collection;
use Modules\Subscriptions\Traits\HasPlanSubscription;

/**
 * @mixin Builder
 */
class Team extends JetstreamTeam
{
    use HasFactory, HasPlanSubscription;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['planSubscription', 'collections'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'personal_team' => 'boolean',
        'disabled' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * @return HasMany
     */
    public function collections(): HasMany
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * @param Collection $collection
     * @return bool
     */
    public function ownsCollection(Collection $collection): bool
    {
        return $this->getKey() == $collection->{$this->getForeignKey()};
    }

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];
}
