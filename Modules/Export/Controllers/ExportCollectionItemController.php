<?php

namespace Modules\Export\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Export\Models\Export;

class ExportCollectionItemController extends Controller
{
    public function index(Request $request, Export $export)
    {
        return response()->json([
            'data' => $export->items()->paginate(5)
        ]);
    }
}
