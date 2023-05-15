<?php

namespace Modules\Imports\Actions;

use Illuminate\Http\UploadedFile;
use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Enums\HeaderTypeEnum;

class ImportImage implements ImportsImage
{
    /**
     * @param Collection $collection
     * @param array $images
     * @return void
     */
    public function import(Collection $collection, array $images): void
    {
        $collection->load('items');

        if ($collection->items->isEmpty()) {
            // add default image header
            $collection->addHeader('image', HeaderTypeEnum::IMAGE);

            $this->addCollectionItems($collection, $images);

            return;
        }

        if ($collection->isImageCollection()) {
            $this->addCollectionItems($collection, $images);

            return;
        }


        foreach ($images as $image) {
            foreach ($collection->items as $item) {
                $replaced = $item->replaceByImageReference($image);
            }
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
            $collectionItem = $collection->items()->create(['data' => []]);

            $collectionItem->data = [
                [
                    'header' => 'image',
                    'path' => $collectionItem->putImage($image),
                ]
            ];

            $collectionItem->save();
        }
    }

    /**
     * @param Collection $collection
     * @param array $images
     * @return void
     */
    protected function replaceImageByReference(Collection $collection, array $images): void
    {
        foreach ($images as $image) {
            foreach ($collection->imageHeaders() as $imageHeader) {
                foreach ($collection->items as $item) {
                    foreach ($item['data'] as $cell) {
                        dd($image->getClientOriginalName());
                    }
                }
            }
        }
    }
}
