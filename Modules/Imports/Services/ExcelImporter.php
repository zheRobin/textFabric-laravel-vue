<?php

namespace Modules\Imports\Services;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\Collections\Models\Collection;
use Modules\Imports\Actions\ImportOnEachRow;
use Modules\Imports\Contracts\Importer;
use Modules\Imports\Contracts\ImportsImagesFromExcel;

class ExcelImporter implements Importer
{
    public function import(Collection $collection): void
    {
        $importer = new ImportOnEachRow($this->getHeaders($collection), $collection);

        $importer->import($collection->importFilePath());

        $imageImporter = app(ImportsImagesFromExcel::class);

        $imageImporter->import($collection);
    }

    protected function getHeaders(Collection $collection): array
    {
        return Arr::flatten(
            (new HeadingRowImport)->toArray(
                $collection->importFilePath(),
            )
        );
    }
}
