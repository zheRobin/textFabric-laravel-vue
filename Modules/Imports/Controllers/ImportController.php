<?php

namespace Modules\Imports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Contracts\StoresImportingFile;
use Modules\Imports\Services\ImporterFactory;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        // validate and store uploaded file
        $uploader = app(StoresImportingFile::class);
        $uploader->store($request->user(), $request->all());

        // get importer
        $importer = (new ImporterFactory)->getImporter(
            $request->user()->currentCollection->importFileExtension()
        );

        // import
        $importer->import($request->user()->currentCollection);
    }

    public function importImages(Request $request)
    {
        // validate

        // dummy images array
        $images = [$request->images ?? null];

        // ImageImporter (collection, array)
        $importer = app(ImportsImage::class);
        $importer->import($request->user()->currentCollection, $images);
    }
}
