<?php

namespace Modules\Translations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Translations\Contracts\TranslatesText;

class TextTranslationController extends Controller
{
    public function index(Request $request)
    {
        $translator = app(TranslatesText::class);

        return response()->json([
            'content' => $translator->translate($request->all()),
        ]);
    }
}
