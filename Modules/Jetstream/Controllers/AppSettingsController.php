<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class AppSettingsController extends Controller
{
    public function index(Request $request)
    {
        Gate::forUser($request->user())->authorize('update-logo');

        return Inertia::render('App/Settings', []);
    }
}
