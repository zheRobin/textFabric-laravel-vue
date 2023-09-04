<?php

namespace Modules\Translations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Contracts\TranslatesText;

class TextTranslationController extends Controller
{
    public function index(Request $request)
    {
        $translator = app(TranslatesText::class);

        $request->user()->currentTeam->planSubscription
            ->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
        return response()->json([
            'content' => $translator->translate($request->all()),
        ]);
    }
}
