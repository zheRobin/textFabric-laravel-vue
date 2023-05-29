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
            'xls', 'xlsx' => new ExcelImporter,
            'xml', 'csv' => new SimpleExcelImporter,
            default => throw new InvalidArgumentException("Wrong importer file extension [${$fileExtension}]")
        };
    }
}
