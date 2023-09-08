<?php

namespace Modules\Imports\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\HeadingRowImport;
use Modules\Collections\Models\Collection;
use Modules\Imports\Actions\ImportOnEachRow;
use Modules\Imports\Contracts\Importer;
use Modules\Imports\Enums\HeaderTypeEnum;

class SimpleExcelImporter implements Importer
{
    public function import(User $user, Collection $collection): void
    {
        $headers = $this->getHeaders($collection);

        foreach ($headers as $index => $header) {
            if($index === 0) {
                $collection->addHeader($header, HeaderTypeEnum::TITLE);
            }else{
                $collection->addHeader($header, HeaderTypeEnum::TEXT);
            }
        }

        $importer = new ImportOnEachRow($user, $this->getHeaders($collection), $collection);

        $importer->import($collection->importFilePath());
    }

    public function getHeaders(Collection $collection): array
    {
        return Arr::flatten(
            (new HeadingRowImport)->toArray(
                $collection->importFilePath(),
            )
        );
    }
}
