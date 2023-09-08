<?php

namespace Modules\Collections\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Collections\Models\Collection;

class CurrentCollectionController extends Controller
{
    public function update(Request $request, Collection $collection)
    {
        if (!$request->user()->switchCollection($collection)) {
            abort(403);
        }

        if ($request->offsetGet('redirect') === 'export') {
            return redirect(route('export.index'), 303);
        }

        return redirect(config('fortify.home'), 303);
    }

    public function itemsIteration(Request $request)
    {
        return response()->json(
            $request->user()->currentCollection->items()->paginate(1)
        );
    }
}
