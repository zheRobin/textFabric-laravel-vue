<?php

namespace Modules\Export\Controllers;

use DeepL\Translator;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Export\Requests\JSONRequest;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Imports\Models\CollectionItem;
use Modules\OpenAI\Contracts\BuildsParams;
use Modules\Presets\Models\Preset;
use OpenAI\Laravel\Facades\OpenAI;
use Modules\Export\Models\Exports;
use Modules\Export\Contracts\CompletesCollectionItem;
use Modules\Export\Requests\ExportRequest;
use Modules\Export\Requests\XMLRequest;
use Spatie\ArrayToXml\ArrayToXml;
use Modules\Export\Helpers\XmlHelper;
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

    public function generate(Request $request, Preset $preset, Exports $exports)
    {
        $builder = app(BuildsParams::class);
        $preset_ids = Compilations::where('id', $request['compilations'])->get()->first()->preset_ids;
        $compilation_name = Compilations::where('id', $request['compilations'])->get()->first()->name;
        $result = [];
        foreach ($preset_ids as $id) {
            $pres = $preset->where('id', $id)->first();
            $lang = Language::get()->where('id', $pres->output_language_id ?? 31)->first()->code;
            $result[$compilation_name . '_' . $pres->name] = [];
            foreach ($request->user()->currentCollection->items()->get() as $index => $item) {
                $params = $builder->build($request->user(), $pres, $item);
                $result[$compilation_name . '_' . $pres->name][$lang][$index] = OpenAI::chat()->create($params)->choices[0]->message->content;
            }
        }
        $id = time();
        $resultData[$id] = [];
        foreach ($result as $name => $value) {
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


        foreach ($request['value'] as $key => $item) {

            $result[$key][key($item)] = $item[key($item)];
            foreach ($item as $text) {
                foreach ($request['languages'] as $index => $lang) {
                    $translate = $translator->translateText($text, null, $lang);
                    $result[$key][$lang] = array_map(fn($el) => $el->text, $translate);
                }
            }
        }

        $data = $exports->find($request['id']);
        $data->value = $result;

        $data->save();
    }

    public function getExport(Request $request)
    {
        return (new ExportRequest)->rules($request['id']);
    }

    public function download(Request $request)
    {
        if($request['format'] === '.xml'){
            $data = (new XMLRequest)->rules($request['id'])[0];
            // Convert array to XML
            $xmlData = XmlHelper::arrayToXml($data);
            $filename = 'data.xml';

            return response($xmlData)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }else if($request['format'] === '.json'){
            $data = (new JSONRequest)->rules($request['id']);
            $filename = 'data.json';
            return response(json_encode($data))
                ->header('Content-Type', 'application/json')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

    }
}
