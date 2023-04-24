<?php

namespace Modules\Subscriptions\Actions;

use Illuminate\Support\Facades\Validator;
use Modules\Subscriptions\Contracts\CreatesPlan;
use Modules\Subscriptions\Models\Plan;

class CreatePlan implements CreatesPlan
{
    /**
     * @param array $input
     * @return Plan
     */
    public function __invoke(array $input): Plan
    {
        $validated = Validator::make($input, [
            'slug' => ['required', 'unique:plans,slug', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'trial_period' => ['sometimes', 'integer', 'max:10000'],
            'invoice_period' => ['sometimes', 'integer', 'max:10000'],
        ]);

        return Plan::create([
            'slug' => $input['slug'],
            'name' => $input['name'],
            'description' => $input['description'],
            'is_active' => $input['is_active'],
            'trial_period' => $input['trial_period'],
            'invoice_period' => $input['invoice_period'],
        ]);
    }
}
