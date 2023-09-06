<?php

namespace Modules\Export\Services;

use Illuminate\Support\Collection;
use Modules\Export\Models\Export;

class RunningCompilationService
{
    /**
     * Retrieve the running compilations in the personal team (this compilation may be run by any member of the personal team).
     *
     * @return Collection
     */
    public function inPersonalTeam(): Collection
    {
        $collections = request()->user()?->personalTeam()?->collections;

        if (!$collections?->count()) {
            return collect();
        }

        return Export::select('id', 'name')
            ->whereIn('collection_id', $collections->pluck('id'))
            ->whereNotNull('job_batch_id')
            ->get();
    }

    /**
     * Retrieve the running compilations in any team (this compilation may be run by any member of any team).
     *
     * @return Collection
     */
    public function inAnyTeam(): Collection
    {
        $collections = request()->user()?->personalTeam()?->collections;
        $currentCollectionId = request()->user()?->currentCollection->id;

        if (!$collections?->count()) {
            return collect(['pending' => 0, 'total' => 0]);
        }

        $exports = Export::select('id', 'collection_id', 'job_batch_id')
            ->with('batch:id,total_jobs,pending_jobs')
            ->whereNotNull('job_batch_id')
            ->orderBy('id')
            ->get();

        // no running compilation
        if ($exports->count() === 0) {
            return collect(['pending' => 0, 'total' => 0]);
        }

        // ours is the only running compilation
        if ($exports->count() === 1) {
            return collect(['pending' => 1, 'total' => 1]);
        }

        $ourCollectionIds = $collections->pluck('id')->all();
        $pending = $exports->count();

        foreach ($exports as $index => $export) {
            // condition to handle the case of single queue worker
            if (in_array($export->collection_id, $ourCollectionIds) && intval($export->collection_id) === intval($currentCollectionId)) {
                // additional condition to handle the case of multiple queue workers
                if ($export->batch->pending_jobs === $export->batch->total_jobs) {
                    $pending = $index;
                }
                break;
            }
        }

        return collect(['pending' => $pending, 'total' => $exports->count()]);
    }
}
