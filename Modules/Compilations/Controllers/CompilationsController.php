<?php

namespace Modules\Compilations\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Translations\Models\Language;

class CompilationsController extends Controller
{
    public function index(Request $request)
    {
        $title = '';
        if($request->user()->currentCollection?->headers) {
            foreach ($request->user()->currentCollection->headers as $item){
                if($item['type'] === 'title'){
                    $title = $item;
                }
            }
        }



        $compilations = Compilations::where('owner', $request->user()->current_team_id)
            ->where('collection_id', $request->user()->currentCollection?->id)
            ->get();

//        dd($compilations);
        // verify collection needed to be picked (or throw exception)
        return Inertia::render('Compilations::Index', [
            'presets' => $request->user()->currentCollection?->presets,
            'previewItem' => boolval($request->user()?->currentCollection?->items()->exists()) ? $request->user()->currentCollection?->items()->get()[0] : null,
            'previewItemLength' => $request->user()?->currentCollection?->items()->exists() ? count($request->user()->currentCollection?->items()->get()) : null,
            'complications' => $compilations,
            'languages' => Language::where('target', '1')
                ->orderBy('name')
                ->get(),
            'hasItems' => boolval($request->user()?->currentCollection?->items()->exists()),
            'permissions' => [
                'canManageCompilations' => Gate::check('manage', Compilations::class)
            ],
            'title' => $title
        ]);
    }

    public function getItem(Request $request): array
    {
        return [
            'item' => $request->user()->currentCollection?->items()->get()[(int)$request['id']]
        ];
    }

    public function store(Request $request)
    {
        $collectionId = $request->user()->currentCollection->id;

        Validator::extend('max_compilations', function ($attribute, $value, $parameters, $validator) {
            $collectionId = $parameters[0];
            $existingCompilationsCount = Compilations::where('collection_id', $collectionId)->count();
            return $existingCompilationsCount < 5;
        }, trans('The maximum number of compilations for this collection has been reached.'));

        $data = $request->validate([
            "name" => [
                'required',
                'string',
                'min:3',
                'max:60',
                Rule::unique('compilations')->where('collection_id', $request->user()->currentCollection->id),
                'max_compilations:' . $collectionId,
            ],
            "preset_ids" => ["array"],
        ]);

        $compilation = new Compilations();
        $compilation->name = $data['name'];
        $compilation->owner = $request->user()->current_team_id;
        $compilation->preset_ids = $data['preset_ids'];
        $compilation->collection_id = $collectionId;

        $compilation->save();

        // Optionally, you can redirect back or perform other actions after saving
        return redirect()->back()->with('success', 'Data saved successfully.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            "id" => ["required", "numeric"],
            "name" => ["string"],
            "owner" => ["numeric"],
            "preset_ids" => ["array"],
        ]);

        $compilation = Compilations::find($data['id']);

        if (!$compilation) {
            return redirect()->back()->withErrors('Compilation not found.');
        }

        if (isset($data['name']) && $data['name'] !== $compilation->name) {
            $request->validate([
                'name' => [
                    'string',
                    'min:3',
                    'max:60',
                    Rule::unique('compilations')->where('collection_id', $request->user()->currentCollection->id),
                ],
            ]);
            $compilation->name = $data['name'];
        }

        $compilation->owner = $data['owner'];
        $compilation->preset_ids = $data['preset_ids'];

        $compilation->save();

        return redirect()->back()->with('success', 'Data saved successfully.');
    }


    public function delete(Request $request)
    {
        $compilation = Compilations::find($request['id']);

        $compilation->delete();
    }
}
