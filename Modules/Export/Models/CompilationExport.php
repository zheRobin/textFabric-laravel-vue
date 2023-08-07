<?php

namespace Modules\Export\Models;

use App\Models\Compilations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompilationExport extends Model
{
    use HasFactory;

    protected $table = 'compilations_export';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'compilation_id',
        'team_id',
        'data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'json',
    ];

    protected $with = [
        'compilation'
    ];

    /**
     * @return BelongsTo
     */
    public function compilation(): BelongsTo
    {
        return $this->belongsTo(Compilations::class);
    }
}
