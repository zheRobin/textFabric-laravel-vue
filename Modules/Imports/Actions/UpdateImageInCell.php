<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Modules\Imports\Contracts\UpdatesImageInCell;
use Modules\Imports\Models\CollectionItem;

class UpdateImageInCell implements UpdatesImageInCell
{
    public function update(User $user, CollectionItem $collectionItem, array $input): void
    {
        Gate::forUser($user)->authorize('update', $collectionItem);

        Validator::make($input, [
            'image' => ['required', File::image()],
            'cell' => ['array'],
        ]);

        $cells = [];
        foreach ($collectionItem->data as $cell) {
            if ($cell['header'] === $input['cell']['header']) {
                if (isset($cell['path'])) {
                    $collectionItem->removeImage($cell['path']);
                }

                $cell['path'] = $collectionItem->putImage($input['image']);
            }

            $cells[] = $cell;
        }

        $collectionItem->update([
            'data' => $cells
        ]);
    }
}
