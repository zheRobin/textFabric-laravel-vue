<?php

namespace Modules\Imports\Contracts;

use Modules\Collections\Models\Collection;

interface ImportsImagesFromExcel
{
    public function import(Collection $collection): void;
}
