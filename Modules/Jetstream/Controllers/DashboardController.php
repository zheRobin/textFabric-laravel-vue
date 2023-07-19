<?php

namespace Modules\Jetstream\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Translations\Models\Language;
use App\Models\Dashboard;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $langCodes = Language::pluck('name', 'code');
//        dd($langCodes);
        return Inertia::render('Jetstream::Dashboard', [
            "languages" => $langCodes
        ]);
    }

    public function update(Request $request)
    {
         $dashboard = new Dashboard;

        dd($dashboard->whereLocales('title', ['en', 'de'])->get());
        $dashboard = new Dashboard;
        $data = $request['data'];

//        $dashboard
//            ->setTranslation('title', 'en', $data['title'])
//            ->setTranslation('title', 'de', $data['title'])
//            ->setTranslation('value', 'en', $data['value'])
//            ->setTranslation('value', 'de', $data['value'])
//            ->setTranslation('link_name', 'en', $data['link']['name'])
//            ->setTranslation('link_name', 'de', $data['link']['name'])
//            ->setTranslation('link', 'en', $data['link']['value'])
//            ->setTranslation('link', 'de', $data['link']['value'])
//            ->save();



        $dashboard->save();
    }
}
