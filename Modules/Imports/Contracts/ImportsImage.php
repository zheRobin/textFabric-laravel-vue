<?php

namespace Modules\Imports\Contracts;

use Illuminate\Http\UploadedFile;
use Modules\Collections\Models\Collection;

interface ImportsImage
{
    /**
     * @param Collection $collection
     * @param UploadedFile[] $images
     * @return void
     */
    public function import(Collection $collection, array $images): void;
}
