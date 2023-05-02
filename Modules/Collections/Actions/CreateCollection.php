<?php

namespace Modules\Collections\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Collections\Contracts\CreatesCollection;
use Modules\Collections\Models\Collection;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class CreateCollection implements CreatesCollection
{
    public function create(User $user, array $input): Collection
    {
        // authorization
        Gate::forUser($user)->authorize('create', Collection::class);

        // validation
        Validator::make($input, [
            'name' => ['required', 'string']
        ])->validateWithBag('createCollection');

        return DB::transaction(function () use ($user, $input) {
            $collection = Collection::create([
                'name' => $input['name'],
                'team_id' => $user->currentTeam->id,
            ]);

            $user->currentTeam->planSubscription
                ->recordFeatureUsage(SubscriptionFeatureEnum::COLLECTIONS_LIMIT);

            $user->switchCollection($collection);

            return $collection;
        });
    }
}
