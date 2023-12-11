<?php

namespace Modules\Export\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Export\Models\Export;
use Modules\Export\Models\ExportCollectionItem;
use Illuminate\Support\Facades\Log;
class ExportCollectionItemController extends Controller


{
    public function index(Request $request, Export $export)
    {
        return response()->json([
            'data' => $export->items()->paginate(5)
        ]);
    }
    public function update(Request $request, Export $export)
    {
        foreach($request->items as $item) {
            $exportItem = ExportCollectionItem::findOrFail($item['id']);
            $exportItem->update([
                'data' => $item['data'],
            ]);
        }

        return back(303);
    }
}
