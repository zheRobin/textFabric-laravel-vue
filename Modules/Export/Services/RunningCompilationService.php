<?php

namespace Modules\Export\Services;

use Illuminate\Support\Collection;
use Modules\Export\Models\Export;

class RunningCompilationService
{
    /**
     * Retrieve the running compilations (a compilation may be run by any member of the team).
     *
     * @return Collection
     */
    public function get(): Collection
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
}
