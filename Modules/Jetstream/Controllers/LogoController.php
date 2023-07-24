<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Jetstream\Contracts\UpdatesLogo;

class LogoController extends Controller
{
    public function store(Request $request)
    {
        $updater = app(UpdatesLogo::class);

        $updater->update($request->user(), $request->all());

        return back(303);
    }
}
