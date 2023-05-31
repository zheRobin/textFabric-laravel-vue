<?php

namespace Modules\Imports\Contracts;

use Illuminate\Http\UploadedFile;
use Modules\Collections\Models\Collection;

interface ImportsImage
{
    /**
     * @param Collection $collection
     * @param array $input
     * @return void
     */
    public function import(Collection $collection, array $input): void;
}
