<?php

namespace Modules\Presets\Actions;

use App\Models\Compilations;
use Modules\Collections\Models\Collection;
use Modules\Presets\Models\Preset;

class CreateDemoPresets
{
    public function create(Collection $collection, string $locale)
    {
        $presets = config("presets.presets.$locale");
        $demoPresets = [];

        foreach ($presets as $preset) {
            $demoPresets[] = Preset::create([
                'collection_id' => $collection->getKey(),
                ...$preset
            ]);
        }

        Compilations::create([
            'name' => 'Website Content',
            'owner' => $collection->team->getKey(),
            'preset_ids' => array_map(fn ($el) => $el->getKey(), $demoPresets)
        ]);
    }
}
