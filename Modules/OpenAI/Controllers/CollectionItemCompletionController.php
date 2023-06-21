<?php

namespace Modules\OpenAI\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\CompletesCollectionItem;
use Modules\Presets\Models\Preset;

class CollectionItemCompletionController extends Controller
{
    public function complete(Request $request, Preset $preset, CollectionItem $item)
    {
        $completer = app(CompletesCollectionItem::class);

        $response = $completer->complete(
            $request->user(),
            $preset,
            $item
        );

        return response()->json(
            $response
        );
    }
}
