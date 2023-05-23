<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Modules\Imports\Contracts\StoresImportingFile;

class StoreImportingFile implements StoresImportingFile
{
    public function store(User $user, array $input): void
    {
        // TODO: add max size validation for regular files and images
        Validator::make($input, [
            'upload' => [
                'required',
                File::types([
                    'xlsx', 'xls', 'csv', 'xml', /*'json',*/
                ]),
            ]
        ])->after(function (\Illuminate\Validation\Validator $validator) use ($user) {
            if (is_null($user->currentCollection)) {
                $validator->errors()->add(
                    'upload', 'You need to select a collection before importing.'
                );
            }
        })->validateWithBag('importFile');

        $user->currentCollection->uploadImportFile($input['upload']);
    }
}
