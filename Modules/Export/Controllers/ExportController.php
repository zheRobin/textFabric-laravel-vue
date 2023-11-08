<?php

namespace Modules\Export\Controllers;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Models\Compilations;
use Modules\Export\Enums\ExportTypeEnum;
use Modules\Export\Jobs\GenerateTranslations;
use Modules\Export\Models\Export;
use Modules\Export\Requests\CSVRequest;
use Modules\Export\Requests\JSONRequest;
use Modules\Export\Requests\XLSXRequest;
use Modules\Export\Services\RunningCompilationService;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Translations\Models\Language;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Export\Helpers\XmlHelper;
use Modules\Export\Jobs\ProcessExportJob;

class ExportController extends Controller
{
    public function index(Request $request, RunningCompilationService $runningCompilationService)
    {
        $currentCollection = $request->user()->currentCollection;

        return Inertia::render('Export::Index', [
            'languages' => Language::select(['name', 'code'])
                ->where('target', '1')
                ->orderBy('name')
                ->get(),
            'compilations' => $currentCollection?->compilations ?? [],
            'activeExport' => $currentCollection?->exports()->active()->first(),
            'hasItems' => boolval($currentCollection?->items()->exists()),
            'teamRunningCompilations' => $runningCompilationService->inCurrentTeam(),
        ]);
    }

    public function search(Request $request)
    {
        $exports = $request->user()->currentCollection
            ->exports()
            ->history()
            ->when(!empty($request->offsetGet('query')), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->offsetGet('query') . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json([
            'data' => $exports
        ]);
    }

    public function cancelled(Request $request)
    {
        $exports = $request->user()->currentCollection
            ->exports()
            ->cancelled()
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return response()->json([
            'data' => $exports
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function generate(Request $request, Compilations $compilation, RunningCompilationService $runningCompilationService)
    {
        abort_if($runningCompilationService->inCurrentTeam()->count() > 0, 403, __('Team has running compilations'));

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
                    // TODO: remove debug message
                    info(sprintf("[%s@%s] Batch %s is cancelled.", get_called_class(), 'generate', $batch->id));
//                    DB::transaction(function () use ($export) {
//                        $export->items()->delete();
//                        $export->delete();
//                    });
                } else if ($batch->hasFailures()) {
                    // TODO: remove debug message
                    info(sprintf("[%s@%s] Batch %s has failed jobs.", get_called_class(), 'generate', $batch->id));
                } else if ($batch->finished()) {
                    // TODO: remove debug message
                    info(sprintf("[%s@%s] Batch %s is finished.", get_called_class(), 'generate', $batch->id));
//                    $export->job_batch_id = null;
//                    $export->save();
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

    /**
     * @throws \Throwable
     */
    public function translate(Request $request, Export $export, RunningCompilationService $runningCompilationService)
    {
        abort_if($runningCompilationService->inCurrentTeam()->count() > 0, 403, __('Team has running compilations'));

        // store languages into export model

        $export->load('items');
        $export->fill(['type' => ExportTypeEnum::TRANSLATION])->save();

        $jobs = $export->items->map(function ($item) use ($request) {
            return new GenerateTranslations($request->user(), $request->offsetGet('languages'), $item);
        });

        $batch = Bus::batch($jobs)
            ->then(function (Batch $batch) use ($export) {
                //
            })
            ->finally(function (Batch $batch) use ($export) {
                if ($batch->cancelled()) {
                    // TODO: remove debug message
                    info(sprintf("[%s@%s] Batch %s is cancelled.", get_called_class(), 'translate', $batch->id));
                    $export->items()->update([
                        'translations' => null
                    ]);
                } else if ($batch->hasFailures()) {
                    // TODO: remove debug message
                    info(sprintf("[%s@%s] Batch %s has failed jobs.", get_called_class(), 'translate', $batch->id));
                } else if ($batch->finished()) {
                    // TODO: remove debug message
                    info(sprintf("[%s@%s] Batch %s is finished.", get_called_class(), 'translate', $batch->id));
//                    $export->job_batch_id = null;
//                    $export->save();
                }
            })
            ->name('Translate Export Compilation')
            ->allowFailures()
            ->dispatch();

        $export->job_batch_id = $batch->id;
        $export->save();

        return [
            'id_queue' => $batch->id
        ];
    }

    public function showProgress(Request $request, RunningCompilationService $runningCompilationService)
    {
        $exports = $request->user()->currentCollection->exports();
        $activeExport = (clone $exports)->active();

        $export = $activeExport->get()->count()
            ? (clone $exports)->active()
            : (clone $exports);

        $export = $export->latest('updated_at')->first();
        $batch = $export?->batch;

        return [
            'data' => [
                'progress' => $batch?->progress() >= 0 ? $batch?->progress() : null,
                'finished' => $batch?->finished() ?? true,
                'cancelled' => $batch?->cancelled() ?? false,
                'successful' => $batch?->pending_jobs === 0 && $batch?->failed_jobs === 0,
                'collection_id' => $export?->collection_id,
                'name' => $export?->name,
                'type' => $export?->type,
                'job_batch_id' => $export?->job_batch_id,
                'compilations' => $runningCompilationService->inAnyTeam(),
            ],
        ];
    }

    public function cancel(Request $request)
    {
        $batchId = $request['id'];

        try {
            $batch = Bus::findBatch($batchId);
            $batch?->cancel();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
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
