<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Modules\Collections\Models\Collection;
use Modules\Jetstream\Traits\HasSuperAdmin;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasSuperAdmin;
    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['currentCollection'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'position',
        'company',
        'employees',
        'email',
        'phone_number',
        'password',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['is_admin'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'is_common_user',
    ];

    /**
     * @return Attribute
     */
    protected function isCommonUser(): Attribute
    {
        return Attribute::make(
            get: fn () => ! $this->isSuperAdmin()
        );
    }

    /**
     * Switch the user's context to the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function switchTeam($team)
    {
        if (! $this->belongsToTeam($team) && !$this->isSuperAdmin()) {
            return false;
        }

        $this->forceFill([
            'current_team_id' => $team->id,
        ])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    /**
     * Get the current collection through team's context.
     *
     * @return BelongsTo|null
     */
    public function currentCollection(): ?BelongsTo
    {
        return $this->belongsTo(Collection::class, 'current_collection_id');
    }

    /**
     * @param Collection $collection
     * @return bool
     */
    public function switchCollection(Collection $collection): bool
    {
        if ($this->currentTeam->ownsCollection($collection)) {
            $this->forceFill([
                'current_collection_id' => $collection->getKey()
            ])->save();

            return true;
        }

        return false;
    }
}
