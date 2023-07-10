<?php

namespace Modules\Translations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Imports\Models\CollectionItem;
use Modules\Translations\Contracts\TranslatesCollectionItemData;

class CollectionItemTranslationController extends Controller
{
    public function index(Request $request, CollectionItem $item)
    {
        $translator = app(TranslatesCollectionItemData::class);

        return response()->json([
            'data' => $translator->translate($item, $request->all()),
        ]);
    }
}
