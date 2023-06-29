<?php

namespace Modules\Compilations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompilationsController extends Controller
{
    public function index(Request $request)
    {
        // verify collection needed to be picked (or throw exception)
        return Inertia::render('Compilations::Index', [
            'items' => [1,2,3,45,5]
        ]);
    }
}
