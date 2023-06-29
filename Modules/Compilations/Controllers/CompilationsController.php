<?php

namespace Modules\Compilations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\OpenAI\Controllers\CollectionItemCompletionController;

class CompilationsController extends Controller
{
    public function index(Request $request)
    {
        // verify collection needed to be picked (or throw exception)
        return Inertia::render('Compilations::Index', [
            'presets' => $request->user()->currentCollection->presets,
            'previewItem' => $request->user()->currentCollection->items()->get(),
        ]);
    }
}
