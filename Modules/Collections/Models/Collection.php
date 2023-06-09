<?php

namespace Modules\Collections\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Imports\Models\CollectionItem;
use Modules\Imports\Traits\HasHeaders;
use Modules\Imports\Traits\HasImport;
use Modules\Presets\Models\Preset;

class Collection extends Model
{
    use HasFactory, HasImport, HasHeaders;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'team_id',
        'headers',
        'last_uploaded_file_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'headers' => 'json',
    ];

    /**
     * Purge all of the collection's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->delete();
    }

    /**
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(CollectionItem::class);
    }

    public function presets(): HasMany
    {
        return $this->hasMany(Preset::class);
    }
}
