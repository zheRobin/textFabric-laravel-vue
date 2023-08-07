<?php

namespace Modules\Export\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Compilations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exports extends Model
{
    use HasFactory;

    protected $table = 'exports';

    protected $fillable = [
        'name',
        'value',
        'batch_id'
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function compilation(): BelongsTo
    {
        return $this->belongsTo(Compilations::class);
    }
}
