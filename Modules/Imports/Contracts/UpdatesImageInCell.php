<?php

namespace Modules\Imports\Contracts;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Modules\Imports\Models\CollectionItem;

interface UpdatesImageInCell
{
    public function update(User $user, CollectionItem $collectionItem, array $input): void;
}
