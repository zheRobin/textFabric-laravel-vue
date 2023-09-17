<?php

namespace Modules\RestApi\Contracts;

use App\Models\Team;
use App\Models\User;
use Modules\Imports\Models\CollectionItem;
use Modules\Presets\Models\Preset;

interface CompletesCollectionItem
{
    public function complete(User|Team $model, Preset $preset, CollectionItem $collectionItem, $translate, $sourceList);
}
