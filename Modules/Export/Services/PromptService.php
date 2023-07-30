<?php

namespace Modules\Export\Services;

use Modules\Collections\Models\Collection;
use Modules\Imports\Enums\HeaderTypeEnum;
use Modules\Imports\Models\CollectionItem;

class PromptService
{
    /**
     * Retrieve valid headers for prompt generation.
     *
     * @param Collection $collection
     * @return array
     */
    public function getHeaders(Collection $collection): array
    {
        $headers = array_filter($collection->headers, function ($header) {
            return $header['type'] !== HeaderTypeEnum::IMAGE->slug();
        });

        return array_column($headers, 'name');
    }

    /**
     * Retrieve valid data for prompt generation.
     *
     * @param CollectionItem $collectionItem
     * @return array
     */
    public function getData(CollectionItem $collectionItem): array
    {
        $headers = $this->getHeaders($collectionItem->collection);

        $promptData = [];

        foreach ($collectionItem->data as $cell) {
            if (isset($cell['value']) &&
                isset($cell['header']) &&
                in_array($cell['header'], $headers)) {
                $promptData[$cell['header']] = $cell['value'];
            }
        }

        return $promptData;
    }
}
