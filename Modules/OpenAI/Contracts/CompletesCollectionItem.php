<?php

namespace Modules\OpenAI\Contracts;

use App\Models\User;
use Modules\Imports\Models\CollectionItem;
use Modules\Presets\Models\Preset;

interface CompletesCollectionItem
{
    public function complete(User $user, Preset $preset, CollectionItem $collectionItem): string;
}
