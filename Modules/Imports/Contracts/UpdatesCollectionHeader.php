<?php

namespace Modules\Imports\Contracts;

use Modules\Collections\Models\Collection;

interface UpdatesCollectionHeader
{
    public function update(Collection $collection, array $input): void;
}
