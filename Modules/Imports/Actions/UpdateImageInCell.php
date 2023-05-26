<?php

namespace Modules\Imports\Actions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Modules\Imports\Contracts\UpdatesImageInCell;
use Modules\Imports\Models\CollectionItem;

class UpdateImageInCell implements UpdatesImageInCell
{
    public function update(CollectionItem $collectionItem, array $input): void
    {
        // authorization

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
