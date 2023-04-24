<?php

namespace Modules\Subscriptions\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Subscriptions\Contracts\UpdatesPlanSubscription;
use Modules\Subscriptions\Models\PlanSubscription;

class PlanSubscriptionController extends Controller
{
    /**
     * @param Request $request
     * @param PlanSubscription $plan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PlanSubscription $plan)
    {
        $updater = app(UpdatesPlanSubscription::class);

        $updater->update($request->user(), $plan, $request->all());

        return back(303);
    }
}
