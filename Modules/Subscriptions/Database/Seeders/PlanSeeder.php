<?php

namespace Modules\Subscriptions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscriptions\Contracts\CreatesPlan;
use Modules\Subscriptions\Models\PlanFeature;

class PlanSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(CreatesPlan $createPlan): void
    {
        $basic = $createPlan([
            'slug' => 'basic',
            'name' => 'Basic plan',
            'description' => null,
            'is_active' => true,
            'trial_period' => 30,
        ]);

        $basic->features()->saveMany([
            new PlanFeature(['slug' => 'basic-collections-limit', 'name' => 'Collections', 'description' => 'up to 1 collection', 'value' => 1]),
        ]);

        $pro = $createPlan([
            'slug' => 'pro',
            'name' => 'Pro plan',
            'description' => null,
            'is_active' => true,
            'trial_period' => 30,
        ]);

        $pro->features()->saveMany([
            new PlanFeature(['slug' => 'pro-collections-limit', 'name' => 'Collections', 'description' => 'up to 5 collections', 'value' => 5]),
        ]);

        $enterprise = $createPlan([
            'slug' => 'enterprise',
            'name' => 'Enterprise pla',
            'description' => null,
            'is_active' => true,
            'trial_period' => 30,
        ]);

        $enterprise->features()->saveMany([
            new PlanFeature(['slug' => 'enterprise-collections-limit', 'name' => 'Collections', 'description' => 'up to 12 collections', 'value' => 12]),
        ]);
    }
}
