<?php

namespace Modules\Imports\Contracts;

use Illuminate\Http\UploadedFile;
use Modules\Imports\Models\CollectionItem;

interface UpdatesImageInCell
{
    public function update(CollectionItem $collectionItem, array $input): void;
}
