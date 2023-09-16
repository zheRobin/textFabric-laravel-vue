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
        try {
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
        } catch (\Exception $e) {
            return new JsonResponse(["message" => $e->getMessage()], 500);
        }
    }

    public function translate(Request $request)
    {
        try {
            $planSubscription = $request->user()->currentTeam->planSubscription;

            if (!$planSubscription->canUseFeature(SubscriptionFeatureEnum::DEEPL_REQUESTS) ||
                !$planSubscription->canUseFeature(SubscriptionFeatureEnum::API_REQUESTS)) {
                $response = [
                    "message" => trans('Your team is out of remaining requests for this month. Please adjust your plan or wait until the next month.'),
                    "timestamp" => now()
                ];
                return new JsonResponse($response, 403);
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

                // ------------------------------------------------
                // count subscription plan ------------------------
                $planSubscription->recordFeatureUsage(SubscriptionFeatureEnum::DEEPL_REQUESTS);
                $planSubscription->recordFeatureUsage(SubscriptionFeatureEnum::API_REQUESTS);
                // ------------------------------------------------

                $result[$lang] = $translatedText->text;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            // Handle other exceptions
            return new JsonResponse(["message" => $e->getMessage()], 500);
        }
    }
}
