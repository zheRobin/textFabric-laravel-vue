<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
class LocalizationController extends Controller
{
    public function changeLanguage(Request $request)
    {
        $locale = $request->input('locale');

        LaravelLocalization::getLocalizedURL($locale, null, [], true);

        // Apply the selected language
        app()->setLocale($locale);

        // Additional actions you need

        return response()->json(['locale' => $locale]);
    }
}
