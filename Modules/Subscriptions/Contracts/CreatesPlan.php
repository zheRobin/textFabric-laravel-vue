<?php

namespace Modules\Subscriptions\Contracts;


use Modules\Subscriptions\Models\Plan;

interface CreatesPlan
{
    /**
     * @param array $input
     * @return Plan
     */
    public function __invoke(array $input): Plan;
}
