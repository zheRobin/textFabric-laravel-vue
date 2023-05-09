<?php

namespace Modules\Imports\Services;

use InvalidArgumentException;
use Modules\Imports\Contracts\Importer;

class ImporterFactory
{
    public function getImporter(string $fileExtension): Importer
    {
        return match ($fileExtension) {
            'json' => new JsonImporter,
            'xls', 'xlsx', 'xml', 'csv' => new ExcelImporter,
            default => throw new InvalidArgumentException("Wrong importer file extension [${$fileExtension}]")
        };
    }
}
