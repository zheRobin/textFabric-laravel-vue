<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\WelcomeData;

class DashboardController extends Controller
{
    public function index(Request $request, WelcomeData $welcomeData)
    {
        return Inertia::render('Jetstream::Dashboard', [
            "data" => $welcomeData->get()
        ]);
    }

    public function update(Request $request, WelcomeData $welcomeData)
    {
        $data = $welcomeData->find($request->id);
        $data->title = $request['title'];
        $data->value = $request['value'];
        $data->link_name = $request['link_name'];
        $data->link = $request['link'];
        $data->icon = $request['icon'];

        $data->save();
    }
}
