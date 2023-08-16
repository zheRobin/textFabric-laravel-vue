<?php

namespace Modules\Export\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExportResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->compilation->name . ' ' . $this->created_at->format('d/m/Y H:s'),
            'batch_id' => $this->batch_id,
            'status' => $this->batch ? 'loading' : 'finish',
            'progress' => $this->batch ? $this->batch->progress() : 100,
        ];
    }
}
