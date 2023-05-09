<?php

namespace Modules\Imports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CollectionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'json',
    ];

    // TODO: this is temporary image saver for the cell (move it to separate trait)
    public function updateCellWithImage(string $externalCoordinates, $imageContent, $extension): void
    {
        $cells = [];

        foreach ($this->data as $cell) {
            if (isset($cell['external_identifier']) &&
                $cell['external_identifier'] === $externalCoordinates) {
                $upload = Storage::disk('teams')->put(
                    "images/$externalCoordinates.$extension",
                    $imageContent
                );

                if ($upload) {
                    $cell['image_path'] = Storage::disk('teams')->path("images/$externalCoordinates.$extension");
                }
            }
            $cells[] = $cell;
        }

        $this->data = $cells;
        $this->save();
    }
}
