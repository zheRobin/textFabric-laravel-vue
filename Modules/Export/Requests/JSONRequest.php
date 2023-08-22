<?php

namespace Modules\Export\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Export\Models\Export;
use Modules\Export\Models\ExportCollectionItem;

class JSONRequest extends FormRequest
{
    public function rules(Export $export)
    {
        $export->load('items');

        return $export->items->map(function (ExportCollectionItem $item) {
            return $item->dataToExport();
        })->toArray();
    }
}
