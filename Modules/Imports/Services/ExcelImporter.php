<?php

namespace Modules\Imports\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Modules\Collections\Models\Collection;
use Modules\Imports\Actions\ImportOnEachRow;
use Modules\Imports\Contracts\Importer;
use Modules\Imports\Contracts\ImportsImagesFromExcel;
use Modules\Imports\Enums\HeaderTypeEnum;

class ExcelImporter implements Importer
{
    public function __construct()
    {
        HeadingRowFormatter::default('none');
    }
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

        // TODO: get headers from collection
        $importer = new ImportOnEachRow($user, $this->getHeaders($collection), $collection);

        $importer->import($collection->importFilePath());

        $imageImporter = app(ImportsImagesFromExcel::class);

        $imageImporter->import($collection);
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
