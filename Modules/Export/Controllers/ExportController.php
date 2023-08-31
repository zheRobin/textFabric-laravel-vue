<?php

namespace Modules\Export\Controllers;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Export\Enums\ExportTypeEnum;
use Modules\Export\Jobs\GenerateTranslations;
use Modules\Export\Models\Export;
use Modules\Export\Requests\CSVRequest;
use Modules\Export\Requests\JSONRequest;
use Modules\Export\Requests\XLSXRequest;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Export\Helpers\XmlHelper;
use Modules\Export\Jobs\ProcessExportJob;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Export::Index', [
            'languages' => Language::where('target', '1')
                ->orderBy('name', 'asc')
                ->get()
                ->pluck('name', 'code'),
            'compilations' => $request->user()->currentCollection->compilations ?? [],
            'activeExport' => $request->user()->currentCollection->exports()->active()->first(),
            'hasItems' => boolval($request->user()?->currentCollection?->items()->exists()),
        ]);
    }

    public function search(Request $request)
    {
        $exports = $request->user()->currentCollection
            ->exports()
            ->history()
            ->where('name', 'LIKE', '%'.$request->offsetGet('query').'%')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json([
            'data' => $exports
        ]);
    }

    public function generate(Request $request, Compilations $compilation)
    {
        $collectionItems = $request->user()->currentCollection->items()->get();

        $export = Export::create([
            'collection_id' => $request->user()->currentCollection->id,
            'name' => Export::buildName($compilation->name),
            'type' => ExportTypeEnum::COMPILATION,
        ]);

        $jobs = $collectionItems->map(function ($item) use ($request, $compilation, $export) {
            return new ProcessExportJob($request->user(), $compilation, $item, $export);
        });

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($export) {
                //
            })
            ->finally(function (Batch $batch) use ($export) {
                if ($batch->cancelled()) {
                    $export->delete();
                }
                if ($batch->hasFailures()) {
                    Artisan::call("queue:retry-batch {$batch->id}");
                }
                if ($batch->finished() || $batch->pendingJobs === 0) {
                    $export->job_batch_id = null;
                    $export->save();
                }
            })
            ->name('Export Compilation')
            ->allowFailures()
            ->dispatch();

        $export->job_batch_id = $batch->id;
        $export->save();

        return [
            'id_queue' => $batch->id
        ];
    }

    public function delete(Request $request, Export $export)
    {
        $export->delete();
    }

    public function translate(Request $request, Export $export)
    {
        // store languages into export model

        $jobs = [];

        $export->load('items');
        $export->fill(['type' => ExportTypeEnum::TRANSLATION])->save();
        $export->items->each(function ($item) use ($request, &$jobs) {
            $jobs[] = new GenerateTranslations($request->offsetGet('languages'), $item);
        });

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($export) {
                //
            })
            ->finally(function (Batch $batch) use ($export) {
                if ($batch->cancelled() || $batch->hasFailures()) {
                    $export->items()->update([
                        'translations' => null
                    ]);
                }
                if ($batch->finished()) {
                    $export->job_batch_id = null;
                    $export->save();
                }
            })
            ->name('Translate Export Compilation')
            ->dispatch();

        $export->job_batch_id = $batch->id;
        $export->save();

        return [
            'id_queue' => $batch->id
        ];
    }

    public function showProgress(Request $request)
    {
        $export = $request->user()->currentCollection->exports()->active()->first();

        return [
            'data' => [
                'progress' => !empty($export->batch) ? $export->batch->progress() : 100,
                'finished' => !empty($export->batch) ? $export->batch->finished() : true,
                'cancelled' => !empty($export->batch) ? $export->batch->cancelled() : false,
                'type' => !empty($export->type) ? $export->type : null,
            ],
        ];
    }

    public function cancel(Request $request){
        $batchId = $request['id'];

        try {
            $batch = Bus::findBatch($batchId);
            if ($batch) {
                $batch->cancel();
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }

        return response()->json([
            'data' => [
                'exportType' => Export::where('job_batch_id', $batchId)->first()?->type
            ]
        ]);
    }

    public function download(Request $request, Export $export)
    {
        if ($request['format'] === '.xml') {
            $data = (new JSONRequest)->rules($export);
            $xmlData = XmlHelper::arrayToXml($data);

            return response($xmlData);
        } else if ($request['format'] === '.json') {
            $data = (new JSONRequest)->rules($export);

            return response(json_encode($data, JSON_PRETTY_PRINT));
        } else if ($request['format'] === '.csv') {
            $data = (new JSONRequest)->rules($export);
            $refactor = (new CSVRequest)->rules($data);

            return response($refactor);
        } else if ($request['format'] === '.xlsx' || $request['format'] === '.xls') {
            $data = (new JSONRequest)->rules($export);
            $refactor = (new XLSXRequest)->convertJsonToXlsx($data);

            return response()->download($refactor);
        }
    }
}
