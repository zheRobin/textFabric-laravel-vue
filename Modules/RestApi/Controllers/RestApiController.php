<?php

namespace Modules\RestApi\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DeepL\Translator;
use Illuminate\Support\Facades\Auth;
use Modules\Imports\Models\CollectionItem;
use Modules\RestApi\Contracts\CompletesCollectionItem;
use Modules\Presets\Models\Preset;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
class RestApiController extends Controller
{
    public function generate(Request $request, Preset $preset, CollectionItem $item)
    {
        $validator = Validator::make($request->all(), [
            'translate-target-list' => ['array'],
            'source-list' => ['array'],
        ]);

        if ($validator->fails()) {
            $response = [
                "message" => "Validation error",
                "errors" => $validator->errors(),
                "timestamp" => now()
            ];

            return new JsonResponse($response, 422); // 422 Unprocessable Entity
        }

        $completer = app(CompletesCollectionItem::class);

        if (!isset($preset->get()->where('id', $request['preset-id'])->where('collection_id', $request->user()->currentCollection->id)->first()->name)) {
            $response = [
                "message" => "Access denied",
                "timestamp" => now()
            ];

            return new JsonResponse($response, 403);
        }

        $response = $completer->complete(
            $request->user(),
            $preset->get()->where('id', $request['preset-id'])->where('collection_id', $request->user()->currentCollection->id)->first(),
            $item->get()->where('collection_id', $request->user()->currentCollection->id)->first(),
            isset($request['translate-target-list']) ? $request['translate-target-list'] : [],
            $request['source-list']
        );

        return response()->json(
            $response
        );
    }

    public function translate(Request $request)
    {
        try{
            $planSubscription = $request->user()->currentTeam->planSubscription;

            if (!$planSubscription->canUseFeature(SubscriptionFeatureEnum::OPENAI_REQUESTS)) {
                $response = [
                    "message" => "Plan limit exceeded",
                    "timestamp" => now()
                ];
                return new JsonResponse($response, 429);
            }

            $validator = Validator::make($request->all(), [
                'translate-target-list' => ['array'],
            ]);

            if ($validator->fails()) {
                $response = [
                    "message" => "Validation error",
                    "errors" => $validator->errors(),
                    "timestamp" => now()
                ];

                return new JsonResponse($response, 422); // 422 Unprocessable Entity
            }

            $result = [];

            $translator = app(Translator::class);

            foreach ($request['translate-target-list'] as $lang) {
                $translatedText = $translator->translateText($request['text'], null, $lang);
                $result[$lang] = $translatedText->text;

                $request->user()->currentTeam->planSubscription
                    ->recordFeatureUsage(SubscriptionFeatureEnum::OPENAI_REQUESTS);
            }

            return $result;
        } catch (\Exception $e) {
            // Handle other exceptions
            return new JsonResponse(["message" => $e->getMessage()], 500);
        }
    }
}
