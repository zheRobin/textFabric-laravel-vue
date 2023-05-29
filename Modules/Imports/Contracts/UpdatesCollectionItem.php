<?php

namespace Modules\Imports\Contracts;

use App\Models\User;
use Modules\Imports\Models\CollectionItem;

interface UpdatesCollectionItem
{
    public function update(User $user, CollectionItem $collectionItem, array $input): void;
}
