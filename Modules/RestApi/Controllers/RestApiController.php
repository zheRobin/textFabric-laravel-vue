<?php

namespace Modules\RestApi\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use DeepL\Translator;
use Illuminate\Support\Facades\Auth;
use Modules\Collections\Models\Collection;
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
    public function generate(Request $request, CollectionItem $item)
    {
        // since API Token belongs to Team (support for User api-token is dropped)
        $team = $request->user();

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

            $teamCollections = Collection::select('id')->where('team_id', $team->id)->pluck('id')->toArray();
            $preset = Preset::query()
                ->where('id', $request['preset-id'])
                ->whereIn('collection_id', $teamCollections)
                ->first();

            if (!isset($preset)) {
                $response = [
                    "message" => "You do not have access to preset id: {$request['preset-id']}",
                    "timestamp" => now()
                ];

                return new JsonResponse($response, 403);
            }

            $response = $completer->complete(
                $team,
                $preset,
                $preset->load('collection.items')->collection->items->first(),
                isset($request['translate-target-list']) ? $request['translate-target-list'] : [],
                $request['source-list']
            );

            return new JsonResponse($response);
        } catch (\Exception $e) {
            return new JsonResponse([
                "message" => $e->getTrace(),
                "timestamp" => now()
            ], 500);
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

            return new JsonResponse($result);
        } catch (\Exception $e) {
            return new JsonResponse([
                "message" => $e->getMessage(),
                "timestamp" => now()
            ], 500);
        }
    }
}
