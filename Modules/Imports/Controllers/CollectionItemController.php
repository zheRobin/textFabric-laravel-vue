<?php

namespace Modules\Imports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Imports\Contracts\UpdatesCollectionItem;
use Modules\Imports\Contracts\UpdatesImageInCell;
use Modules\Imports\Models\CollectionItem;

class CollectionItemController extends Controller
{
    public function update(Request $request, CollectionItem $collectionItem)
    {
        $updater = app(UpdatesCollectionItem::class);

        $updater->update($request->user(), $collectionItem, $request->all());

        return back(303);
    }

    public function updateImage(Request $request, CollectionItem $collectionItem)
    {
        $updater = app(UpdatesImageInCell::class);

        $updater->update($request->user(), $collectionItem, $request->all());

        return back(303);
    }
}
