<?php

namespace Modules\Imports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\UpdatesCollectionHeader;

class CollectionHeaderController extends Controller
{
    public function update(Request $request, Collection $collection)
    {
        $updater = app(UpdatesCollectionHeader::class);

        $updater->update($collection, $request->all());

        return back(303);
    }
}
