<?php

namespace Modules\Imports\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Modules\Collections\Models\Collection;
use Modules\Imports\Contracts\Importer;
use Modules\Imports\Models\CollectionItem;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;

class JsonImporter implements Importer
{
    /**
     * @var int
     */
    public const JSON_MAX_DEPTH = 3;

    /**
     * @throws ValidationException
     */
    public function import(User $user, Collection $collection): void
    {
        if($collection->importFileExtension() === 'xml'){
            $data = simplexml_load_string($collection->importFileContent());
            $result = [];
            foreach ($data as $item){
                $result[] = json_decode(json_encode($item), true);

            }
            $data = $result;
        }else{
            $data = $this->validateJsonStructure($collection->importFileContent());
        }

        if (empty($data)) {
            throw ValidationException::withMessages([
                'upload' => [__('import.validation.wrong-format')],
            ])->errorBag('importFile');
        }

        $this->validate($user, $collection, $data);

        $headers = array_keys(current($data));

        $headerItems = [];
        foreach ($headers as $index => $header) {
            if($index === 0){
                $headerItems[] = [
                    'name' => $header,
                    'type' => 'title',
                ];
            }else{
                $headerItems[] = [
                    'name' => $header,
                    'type' => 'text',
                ];
            }
        }

        $collection->forceFill(['headers' => $headerItems])->save();

        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }

            $collectionItem = new CollectionItem;
            $collectionItemCells = [];

            foreach ($row as $key => $value) {
                if (in_array($key, $headers)) {
                    $collectionItemCells[] = [
                        'header' => $key,
                        'value' => $value
                    ];
                }
            }

            $collectionItem->data = $collectionItemCells;
            $collection->items()->save($collectionItem);
        }
    }

    public function getHeaders(Collection $collection): array
    {

        $data = $this->validateJsonStructure($collection->importFileContent());

        if (empty($data)) {
            return [];
        }

        return array_keys(current($data));
    }

    /**
     * @throws ValidationException
     */
    protected function validate(User $user, Collection $collection, array $data)
    {
        $planSubscription = $user->currentTeam->planSubscription;
        if (!$planSubscription->featureAllowsValue(
            SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT, count($data))
        ) {
            $collection->removeImportedFile();
            throw ValidationException::withMessages([
                'upload' => [__('import.validation.max-items', [
                    'number' => $planSubscription->getFeatureValue(
                        SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT)
                ])],
            ])->errorBag('importFile');
        }
    }

    protected function validateJsonStructure($data): array|false
    {
        $array = json_decode(
            $data,
            true,
            self::JSON_MAX_DEPTH,
        );

        if (empty($array) || !is_array($array)) {
            return false;
        }

        if (!is_array(current($array))) {
            return false;
        }

        $headers = array_filter(array_keys(current($array)), function ($item) {
            return $item && is_string($item);
        });

        if (empty($headers)) {
            return false;
        }

        return $array;
    }
}
