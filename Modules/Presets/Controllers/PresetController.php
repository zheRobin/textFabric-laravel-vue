<?php

namespace Modules\Presets\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Jetstream\RedirectsActions;
use Modules\Presets\Contracts\CreatesPreset;
use Modules\Presets\Contracts\UpdatesPreset;
use Modules\Presets\Data\PresetInput;
use Modules\Presets\Models\Preset;

class PresetController extends Controller
{
    use RedirectsActions;

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, PresetInput $data)
    {
        $creator = app(CreatesPreset::class);

        $preset = $creator->create($request->user(), $data);

        return $this->redirectPath($creator)->with(['preset' => $preset]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Preset $preset, PresetInput $presetData)
    {
        $updater = app(UpdatesPreset::class);

        $updater->update($request->user(), $preset, $presetData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
