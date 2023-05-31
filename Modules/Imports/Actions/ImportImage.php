<?php

namespace Modules\Imports\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Enums\HeaderTypeEnum;

class ImportImage implements ImportsImage
{
    /**
     * @param Collection $collection
     * @param array $input
     * @return void
     */
    public function import(Collection $collection, array $input): void
    {
        Validator::make($input, [
            'upload' => ['required', 'array'],
            'upload.*' => [
                'required',
                File::image()
                    ->max(5 * 1024)
            ]
        ])->validateWithBag('updateCollectionItemImage');

        if ($collection->items->isEmpty()) {
            $collection->addHeader('image', HeaderTypeEnum::IMAGE);

            $this->addCollectionItems($collection, $input['upload']);

            return;
        }

        if ($collection->isImageCollection()) {
            $this->addCollectionItems($collection, $input['upload']);

            return;
        }

        $collection->load('items');

        $insertImages = [];

        foreach ($input['upload'] as  $image) {
            $replaced = false;

            foreach ($collection->items as $item) {
                $replaced = $item->replaceByImageReference($image);
            }

            if (!$replaced) {
                $insertImages[] = $image;
            }
        }

        if (!empty($insertImages)) {
            $this->addCollectionItems($collection, $insertImages);
        }
    }

    /**
     * @param Collection $collection
     * @param UploadedFile[] $images
     * @return void
     */
    protected function addCollectionItems(Collection $collection, array $images): void
    {
        foreach ($images as $image) {
            $collectionItem = $collection->items()->create(['data' => $collection->newRow()]);

            $data = $collectionItem->data;
            $cells = [];

            foreach ($data as $cell) {
                if (strcasecmp($cell['header'], HeaderTypeEnum::IMAGE->slug()) === 0) {
                    $cell['path'] = $collectionItem->putImage($image);
                }
                $cells[] = $cell;
            }

            $collectionItem->data = $cells;

            $collectionItem->save();
        }
    }
}
