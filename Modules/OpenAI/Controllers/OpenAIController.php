<?php

namespace Modules\OpenAI\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\OpenAI\Enums\ChatModelEnum;
use Modules\Translations\Models\Language;
use Modules\Presets\Models\Preset;

class OpenAIController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('OpenAI::Editor',[
            'presets' => $request->user()->currentCollection->presets,
            'selectedPreset' => session('preset'),
            'previewItem' => $request->user()->currentCollection->items()->first(),
            'models' => ChatModelEnum::values(),
            'languages' => Language::all(),
        ]);
    }

    public function update(Request $request, Preset $preset): void
    {
        $change = $preset->find($request['id']);
        $change['name'] = $request['presets'];

        $change->save();
    }

    public function delete(Request $request, Preset $preset): void
    {
        $change = $preset->find($request->input('id'));
        $change->delete();
    }
}
