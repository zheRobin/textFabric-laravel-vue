<?php

namespace Modules\Imports\Contracts;

use App\Models\User;
use Modules\Collections\Models\Collection;

interface ImportsImage
{
    /**
     * @param Collection $collection
     * @param array $input
     * @return void
     */
    public function import(User $user, Collection $collection, array $input): void;
}
