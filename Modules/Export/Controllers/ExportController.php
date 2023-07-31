<?php

namespace Modules\Export\Controllers;

use DeepL\Translator;

use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Export\Requests\CSVRequest;
use Modules\Export\Requests\JSONRequest;
use Modules\Export\Requests\XLSXRequest;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\OpenAI\Contracts\BuildsParams;
use Modules\Presets\Models\Preset;
use OpenAI\Laravel\Facades\OpenAI;
use Modules\Export\Models\Exports;
use Modules\Export\Requests\ExportRequest;
use Modules\Export\Requests\XMLRequest;
use Modules\Export\Helpers\XmlHelper;
use App\Events\ProgressUpdateEvent;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Export::Index', [
            'languages' => Language::get()->pluck('name', 'code'),
            'complications' => Compilations::where('owner', $request->user()->current_team_id)->get(),
            'exports' => Exports::paginate(10),
            'exportCount' => count(Exports::get())
        ]);
    }

    public function search(Request $request)
    {
        $query = $request['query'];

        return Exports::where('name', 'LIKE', "%$query%")->paginate(10);
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
                $response = OpenAI::chat()->create($params);
                $content = $response->choices[0]->message->content;
                $result[$compilation_name . '_' . $pres->name][$lang][$index] = $content;
            }
        }
        $id = \Carbon\Carbon::now()->format('h:i');
        $resultData[$id] = [];
        foreach ($result as $name => $value) {
            $resultData[$id][$name] = $value;
        }

        $exports->name = $compilation_name . '_' . $id;
        $exports->value = $resultData[$id];

        $exports->save();

        return Exports::paginate(10);
    }


    public function delete(Request $request, Exports $exports)
    {
        $exports->where('id', $request['id'])->delete();

        return Exports::paginate(10);
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
        $data = (new ExportRequest)->rules($request['id']);
        $extractedData = [];
        foreach ($data as $subArray) {
            foreach ($subArray as $key => $messages) {
                $extractedData[$key] = array_slice($messages, 0, 3);
            }
        }
        return [
            'export' => $extractedData,
            'count' => count($data[0][array_keys($data[0])[0]])
        ];
    }

    public function pagination(Request $request)
    {
        $data = (new ExportRequest)->rules($request['id']);
        $extractedData = [];
        foreach ($data as $subArray) {
            foreach ($subArray as $key => $messages) {
                $extractedData[$key] = array_slice($messages, $request['page'] * 3, 3);
            }
        }
        return [
            'export' => $extractedData,
        ];
    }

    public function download(Request $request)
    {
        if($request['format'] === '.xml')
        {
            $data = (new XMLRequest)->rules($request['id'])[0];
            $xmlData = XmlHelper::arrayToXml($data);

            return response($xmlData);
        }
        else if($request['format'] === '.json')
        {
            $data = (new JSONRequest)->rules($request['id']);

            return response(json_encode($data));
        }
        else if($request['format'] === '.csv')
        {
            $data = (new JSONRequest)->rules($request['id']);
            $refactor = (new CSVRequest)->rules($data);

            return response($refactor);
        }
        else if($request['format'] === '.xlsx' || $request['format'] === '.xls')
        {
            $data = (new JSONRequest)->rules($request['id']);
            $refactor = (new XLSXRequest)->convertJsonToXlsx($data);

            return response()->download($refactor);
        }
    }
}
