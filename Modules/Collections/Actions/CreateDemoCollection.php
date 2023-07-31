<?php

namespace Modules\Collections\Actions;

use App\Models\Team;
use App\Models\User;
use Modules\Collections\Models\Collection;
use Modules\Presets\Actions\CreateDemoPresets;

class CreateDemoCollection
{
    public function create(User $user, Team $team, string $locale): void
    {
        $collection = config("collection.$locale");

        $demoCollection = Collection::create([
            'name' => $collection['name'],
            'headers' => $collection['headers'],
            'team_id' => $team->getKey(),
        ]);

        $user->switchCollection($demoCollection);

        foreach ($collection['items'] as $item) {
            $demoCollection->items()->create([
                'data' => $item
            ]);
        }

        $demoPresetsCreator = app(CreateDemoPresets::class);
        $demoPresetsCreator->create($demoCollection, $locale);
    }
}
