<?php

namespace Modules\OpenAI\Contracts;

use App\Models\User;
use Modules\Imports\Models\CollectionItem;
use Modules\Presets\Models\Preset;

interface BuildsParams
{
    public function build(User $user, Preset $preset, CollectionItem $collectionItem): array;
}
