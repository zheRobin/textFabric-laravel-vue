<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
class ApiController extends Controller
{
    public function index(Request $request): Response
    {

        // verify collection needed to be picked (or throw exception)
        return Inertia::render('Api::Index', [
            'items' => ApiTokens::where('user_id', Auth::user()->id)->get()
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'permission' => ['required', 'array'],
            'permission.*' => ['boolean'],
        ]);

        $apiToken = ApiTokens::create([
            'name' => $data['name'],
            'create' => $data['permission']['create'] ?? false,
            'read' => $data['permission']['read'] ?? false,
            'delete' => $data['permission']['delete'] ?? false,
            'update' => $data['permission']['update'] ?? false,
            'user_id' => Auth::user()->id,
        ]);

        return Redirect::route('api.index')->with('message', 'Entry created successfully');
    }
}
