<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function index()
    {
        return Inertia::render('Teams/Index', []);
    }
}
