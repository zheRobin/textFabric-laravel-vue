<?php

namespace Modules\Imports\Contracts;

use Modules\Collections\Models\Collection;

interface Importer
{
    public function import(Collection $collection);
}
