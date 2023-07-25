<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Modules\Imports\Contracts\StoresImportingFile;
use Modules\Imports\Services\ImporterFactory;

class StoreImportingFile implements StoresImportingFile
{
    public function store(User $user, array $input): void
    {
        Gate::forUser($user)->authorize('update', $user->currentCollection);

        Validator::make($input, [
            'upload' => [
                'required',
                File::types(['xlsx', 'xls', 'csv', 'xml', 'json', 'txt'])
                    ->max(5 * 1024),
            ],
            'append' => ['required', 'boolean']
        ])->after(function (\Illuminate\Validation\Validator $validator) use ($user, $input) {
            if (is_null($user->currentCollection)) {
                $validator->errors()->add(
                    'upload', 'You need to select a collection before importing.'
                );
            }

            // validate headers
            if ($input['append'] && $user->currentCollection->items->isNotEmpty()) {
                $user->currentCollection->uploadImportFile($input['upload']);

                $importer = (new ImporterFactory)
                    ->getImporter($user->currentCollection->importFileExtension());

                $importedHeaders = $importer->getHeaders($user->currentCollection);

                $extraHeaders = array_diff(
                    $importedHeaders,
                    array_column($user->currentCollection->headers, 'name')
                );

                if (empty($importedHeaders) || !empty($extraHeaders)) {
                    $user->currentCollection->removeImportedFile();

                    $validator->errors()->add(
                        'upload', 'Headers cannot be matched.'
                    );
                }
            }
        })->validateWithBag('importFile');

        $user->currentCollection->uploadImportFile($input['upload']);
    }
}
