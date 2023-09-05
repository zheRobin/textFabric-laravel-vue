<?php

namespace Modules\Presets\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Modules\Collections\Models\Collection;
use Modules\Presets\Contracts\CreatesPreset;
use Modules\Presets\Data\PresetInput;
use Modules\Presets\Models\Preset;
use Illuminate\Validation\Rule;

class CreatePreset implements CreatesPreset
{
    public function create(User $user, PresetInput $presetInput): Preset
    {
        $collection = Collection::find($presetInput->collection_id);
        Gate::forUser($user)->authorize('create', [Preset::class, $collection]);

        $existingPresetsCount = Preset::where('collection_id', $presetInput->collection_id)->count();


        Validator::extend('max_presets', function ($attribute, $value, $parameters, $validator) {
            $collectionId = $parameters[0];
            $existingPresetsCount = Preset::where('collection_id', $collectionId)->count();
            return $existingPresetsCount < 5;
        }, 'The maximum number of presets for this collection has been reached.');

        Validator::make($presetInput->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('presets')->where('collection_id', $presetInput->collection_id),
                'max_presets:' . $presetInput->collection_id,
            ]
        ])->validate();

        return Preset::create($presetInput->all());
    }


    public function redirectTo(): string
    {
        return route('editor.index');
    }
}
