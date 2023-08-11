<?php

namespace Modules\Export\Controllers;

use DeepL\Translator;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Export\Jobs\GenerateTranslations;
use Modules\Export\Models\CompilationExport;
use Modules\Export\Requests\CSVRequest;
use Modules\Export\Requests\JSONRequest;
use Modules\Export\Requests\XLSXRequest;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Export\Requests\ExportRequest;
use Modules\Export\Requests\XMLRequest;
use Modules\Export\Helpers\XmlHelper;
use Modules\Export\Jobs\GenerateExports;
use Modules\Export\Models\QueueProgress;
use Modules\Collections\Models\Collection;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        $compilations = Compilations::where([
            'owner' => $request->user()->current_team_id,
            'collection_id' => $request->user()->currentCollection->id,
        ])->get();

        return Inertia::render('Export::Index', [
            'languages' => Language::get()->pluck('name', 'code'),
            'complications' => $compilations,
            'exports' => CompilationExport::orderBy('id', 'DESC')->where('collection_id', $request->user()->currentCollection->id)->paginate(10),
            'exportCount' => count(CompilationExport::get()),
            'hasItems' => boolval($request->user()?->currentCollection?->items()->exists())
        ]);
    }

    public function search(Request $request)
    {
        $query = $request['query'];

        return CompilationExport::where('name', 'LIKE', "%$query%")->where('collection_id', $request->user()->currentCollection->id)->orderBy('id', 'DESC')->paginate(10);
    }

    public function generate(Request $request)
    {
        $compilationId = $request->get('compilations');
        $items = $request->user()->currentCollection->items()->get();

        $job = new GenerateExports($compilationId, $items, $request->user()->id, $request->user()->current_team_id, $request->user()->currentCollection->id);
        $dispatch = Bus::dispatch($job);

        return [
            'id_queue' => $dispatch
        ];
    }


    public function delete(Request $request, CompilationExport $exports)
    {
        $exports->where('id', $request['id'])->delete();

        return CompilationExport::orderBy('id', 'DESC')->where('collection_id', $request->user()->currentCollection->id)->paginate(10);
    }

    public function translation(Request $request, CompilationExport $exports)
    {
        $job = new GenerateTranslations($request['value'], $request['languages'], $request['id']);
        $dispatch = Bus::dispatch($job);

        return [
            'id_queue' => $dispatch
        ];
    }

    public function showProgress(Request $request)
    {
        $id = $request['id'];
        $progress = QueueProgress::where('job_id', $id)->get();
        return [
            'progress' => $progress[0]->progress
        ];
    }

    public function getExport(Request $request)
    {
        $imports = DB::table('collection_items')->where('collection_id', $request->user()->currentCollection->id)->get();

        $data = (new ExportRequest)->rules($request['id'], $imports);
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

    public function cancel(Request $request){
        $jobId = $request['id'];

        $queueProgress = QueueProgress::where('job_id', $jobId)->first(); // Use first() to get a single record
        $queueProgress->status = 'stop';
        $queueProgress->save();
    }

    public function pagination(Request $request)
    {
        $imports = DB::table('collection_items')->where('collection_id', $request->user()->currentCollection->id)->get();

        $data = (new ExportRequest)->rules($request['id'], $imports);
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
        $imports = DB::table('collection_items')->where('collection_id', $request->user()->currentCollection->id)->get();
        if($request['format'] === '.xml')
        {
            $data = (new JSONRequest)->rules($request['id'], $imports);
            $xmlData = XmlHelper::arrayToXml($data);

            return response($xmlData);
        }
        else if($request['format'] === '.json')
        {
            $data = (new JSONRequest)->rules($request['id'], $imports);

            return response(json_encode($data, JSON_PRETTY_PRINT));
        }
        else if($request['format'] === '.csv')
        {
            $data = (new JSONRequest)->rules($request['id'], $imports);
            $refactor = (new CSVRequest)->rules($data);

            return response($refactor);
        }
        else if($request['format'] === '.xlsx' || $request['format'] === '.xls')
        {
            $data = (new JSONRequest)->rules($request['id'], $imports);
            $refactor = (new XLSXRequest)->convertJsonToXlsx($data);

            return response()->download($refactor);
        }
    }
}
