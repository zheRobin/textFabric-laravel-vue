<?php

namespace Modules\Export\Controllers;

use Carbon\Carbon;
use DeepL\Translator;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Export\Jobs\GenerateTranslations;
use Modules\Export\Models\CompilationExport;
use Modules\Export\Requests\CSVRequest;
use Modules\Export\Requests\JSONRequest;
use Modules\Export\Requests\XLSXRequest;
use Modules\Presets\Models\Preset;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Export\Requests\ExportRequest;
use Modules\Export\Helpers\XmlHelper;
use Modules\Export\Jobs\ProccesExportJob;
use Modules\Export\Models\QueueProgress;
use Modules\Export\Http\Resources\ExportResource;
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
            'hasItems' => boolval($request->user()?->currentCollection?->items()->exists()),
            'activeExports' => ExportResource::collection(CompilationExport::active()->where('team_id', $request->user()->current_team_id)->get())->collection,
            'items' =>  $request->user()->currentCollection?->items()->paginate(5)->onEachSide(2)
        ]);
    }

    public function search(Request $request)
    {
        $query = $request['query'];

        return CompilationExport::where('name', 'LIKE', "%$query%")->where('collection_id', $request->user()->currentCollection->id)->orderBy('id', 'DESC')->paginate(10);
    }

    public function generate(Request $request, Preset $preset)
    {
        $compilationId = $request->get('compilations');
        $user = $request->user();
        $compilationModel = Compilations::where('id', $compilationId)->first();

        $items = $request->user()->currentCollection->items()->get();
        $teamID = $request->user()->current_team_id;
        $collection_id = $request->user()->currentCollection->id;

        $compilationName = $compilationModel->name;
        $presetIds = $compilationModel->preset_ids;
        $currentDateTime = Carbon::now();
        $export = new CompilationExport();
        $export->compilation_id = $compilationId;
        $export->team_id = $user->current_team_id;
        $export->name = $compilationName . '_' . $currentDateTime->format('Y-m-d H:i:s');
        $export->data = [];
        $export->collection_id = $collection_id;
        $export->save();

        $result = [];
        $count = 1;
        $jobs = [];
        foreach ($presetIds as $id) {
            $pres = $preset->where('id', $id)->first();
            $result[$compilationName . '_' . $pres->name] = [];
            foreach ($items as $index => $item) {
                $jobs[] = new ProccesExportJob($user, $pres, $item, $export, $compilationModel->name);
            }
        }
        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($export) {
                $export->batch_id = null;
                $export->save();
            })
            ->name('Export Compilation')
            ->dispatch();

        $export->batch_id = $batch->id;
        $export->save();

        return [
            'id_queue' => $batch->id
        ];
    }


    public function delete(Request $request, CompilationExport $exports)
    {
        $exports->where('id', $request['id'])->delete();

        return CompilationExport::orderBy('id', 'DESC')->where('collection_id', $request->user()->currentCollection->id)->paginate(10);
    }

    public function translation(Request $request, CompilationExport $exports)
    {
//        $job = new GenerateTranslations($request['value'], $request['languages'], $request['id']);
//        $dispatch = Bus::dispatch($job);
//
//        return [
//            'id_queue' => $dispatch
//        ];
        $result = [];
        $jobs = [];
        $export = $exports->find($request['id']);

        $translator = app(Translator::class);
        $count = 1;
        foreach ($request['value'] as $key => $item) {
            $result[$key][key($item)] = $item[key($item)];
            foreach ($item as $textkey =>$text) {
                foreach ($text as $lang => $value){
                    $jobs[] = new GenerateTranslations($request['languages'], $value, $key, $export, $textkey);
                }
            }
        }

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($export) {
                $export->batch_id = null;
                $export->save();
            })
            ->name('Export Compilation')
            ->dispatch();

        $export->batch_id = $batch->id;
        $export->save();

        return [
            'id_queue' => $batch->id
        ];
    }

    public function showProgress(Request $request)
    {

        $batch = CompilationExport::active()->where('team_id', $request->user()->current_team_id)->get()->first();
        $progress = $batch ? $batch->batch->total_jobs > 0 ? round(($batch->batch->processedJobs() / $batch->batch->total_jobs) * 100) : 0 : 100;

        return [
            'progress' => $progress
        ];
    }

    public function cancel(Request $request){
        $batchId = $request['id'];

        try {
            $batch = Bus::findBatch($batchId);
            if ($batch) {
                $batch->cancel();
            }
            CompilationExport::where('batch_id', $batchId)->delete();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }

        return Redirect::route('export.index')->with([
            'exports' => ExportResource::collection(CompilationExport::history()->where('team_id', $request->user()->current_team_id)->paginate(10)),
            'activeExports' => ExportResource::collection(CompilationExport::active()->where('team_id', $request->user()->current_team_id)->get())->collection,
        ]);
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

    public function getExport(Request $request)
    {
        $imports = DB::table('collection_items')->where('collection_id', $request->user()->currentCollection->id)->get();

        $data = (new ExportRequest)->rules($request['id'], $imports);
        $extractedData = [];
        foreach ($data as $subArray) {
            foreach ($subArray as $key => $messages) {
                $extractedData[$key] = array_slice($messages, 0, 5);
            }
        }

        return [
            'export' => $extractedData,
            'count' => count($data[0][array_keys($data[0])[0]])
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
