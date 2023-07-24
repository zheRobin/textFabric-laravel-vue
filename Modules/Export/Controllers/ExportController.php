<?php

namespace Modules\Export\Controllers;

use DeepL\Translator;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsParams;
use Modules\Presets\Models\Preset;
use OpenAI\Laravel\Facades\OpenAI;
use Modules\Export\Models\Exports;
class ExportController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Export::Index', [
            'languages' => Language::get()->pluck('name', 'code'),
            'complications' => Compilations::where('owner', $request->user()->current_team_id)->get(),
            'exports' => Exports::get()
        ]);
    }

    public function generate(Request $request, Preset $preset, CollectionItem $item, Exports $exports){
        $builder = app(BuildsParams::class);
        $preset_ids = Compilations::where('id', $request['compilations'])->get()->first()->preset_ids;
        $compilation_name = Compilations::where('id', $request['compilations'])->get()->first()->name;
        $result = [];
        foreach ($preset_ids as $id){
            $pres = $preset->where('id', $id)->first();
            $lang = Language::get()->where('id', $pres->output_language_id ?? 31)->first()->code;
            $result[$compilation_name.'_'.$pres->name] = [];
            foreach ($item->get() as $index => $item){
                $params = $builder->build($request->user(), $pres, $item);
                $result[$compilation_name.'_'.$pres->name][$lang][$index] = OpenAI::chat()->create($params)->choices[0]->message->content;
            }
        }
        $id = time();
        $resultData[$id] = [];
        foreach ($result as $name => $value){
            $resultData[$id][$name] = $value;
        }

        $exports->name = $id;
        $exports->value = $resultData[$id];

        $exports->save();
    }

    public function delete(Request $request, Exports $exports)
    {
        $exports->where('id', $request['id'])->delete();
    }

    public function translation(Request $request, Exports $exports)
    {
        $result = [];


        $translator = app(Translator::class);


        foreach ($request['value'] as $key => $item){

            $result[$key][key($item)] = $item[key($item)];
            foreach ($item as $text){
                foreach ($request['languages'] as $index => $lang) {
                    $translate = $translator->translateText($text, null, $lang);
                    $result[$key][$lang] = array_map(fn ($el) => $el->text, $translate);
                }
            }
        }

        $data = $exports->find($request['id']);
        $data->value = $result;

        $data->save();
    }
}
