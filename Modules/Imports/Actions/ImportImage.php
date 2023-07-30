<?php

namespace Modules\Imports\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\ImportsImage;
use Modules\Imports\Enums\HeaderTypeEnum;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class ImportImage implements ImportsImage
{
    /**
     * @param Collection $collection
     * @param array $input
     * @return void
     */
    public function import(User $user, Collection $collection, array $input): void
    {
        Gate::forUser($user)->authorize('update', $collection);

        Validator::make($input, [
            'upload' => ['required', 'array'],
            'upload.*' => [
                'required',
                File::image()
                    ->max(5 * 1024)
            ]
        ])->validateWithBag('importFile');

        if ($collection->items->isEmpty()) {
            $collection->addHeader('image', HeaderTypeEnum::IMAGE);

            $this->addCollectionItems($user, $collection, $input['upload']);

            return;
        }

        if ($collection->isImageCollection()) {
            $this->addCollectionItems($user, $collection, $input['upload']);

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
            $this->addCollectionItems($user, $collection, $insertImages);
        }
    }

    /**
     * @param Collection $collection
     * @param UploadedFile[] $images
     * @return void
     */
    protected function addCollectionItems(User $user, Collection $collection, array $images): void
    {
        $planSubscription = $user->currentTeam->planSubscription;
        if (!$planSubscription->featureAllowsValue(
            SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT, $collection->items->count() + count($images))
        ) {
            throw ValidationException::withMessages([
                'upload' => [__('import.validation.max-items', [
                    'number' => $planSubscription->getFeatureValue(
                        SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT)
                ])],
            ])->errorBag('importFile');
        }

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
