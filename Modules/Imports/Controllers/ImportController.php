<?php

namespace Modules\Imports\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Contracts\StoresImportingFile;
use Modules\Imports\Services\ImporterFactory;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        // verify collection needed to be picked (or throw exception)

        return Inertia::render('Imports::Index', [
//            'headers' => $request->user()->currentCollection->headers,
            'items' =>  $request->user()->currentCollection?->items()->paginate(5)->onEachSide(2)
        ]);
    }

    public function store(Request $request)
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
        // TODO: validation

        // dummy images array
        $images = [$request->upload ?? null];

        // ImageImporter (collection, array)
        $importer = app(ImportsImage::class);
        $importer->import($request->user()->currentCollection, $images);
    }
}
