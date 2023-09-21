<?php

namespace Modules\Collections\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Modules\Collections\Contracts\DeletesCollection;
use Modules\Collections\Models\Collection;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class DeleteCollection implements DeletesCollection
{

    public function delete(User $user, Collection $collection): void
    {
        Gate::forUser($user)->authorize('delete', $collection);

        DB::transaction(function () use ($user, $collection) {
            $collection->purge();

            $user->currentTeam->planSubscription
                ->reduceFeatureUsage(SubscriptionFeatureEnum::COLLECTIONS_LIMIT);

            $collectionDirectory = "storage/team-{$collection->team_id}/collection-{$collection->id}";
            if (File::exists(public_path($collectionDirectory))) {
                File::deleteDirectory(public_path($collectionDirectory));
            }
        });
    }
}
