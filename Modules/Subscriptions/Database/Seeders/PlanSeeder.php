<?php

namespace Modules\Subscriptions\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subscriptions\Contracts\CreatesPlan;
use Modules\Subscriptions\Enums\ResettableIntervalEnum;
use Modules\Subscriptions\Enums\SubscriptionFeatureEnum;
use Modules\Subscriptions\Enums\SubscriptionPlanEnum;
use Modules\Subscriptions\Models\PlanFeature;

class PlanSeeder extends Seeder
{
    /**
     * Run the Database seeds.
     */
    public function run(CreatesPlan $createPlan): void
    {
        $basic = $createPlan([
            'slug' => SubscriptionPlanEnum::BASE->slug(),
            'name' => 'Basic plan',
            'description' => null,
            'is_active' => true,
            'trial_period' => 14,
            'invoice_period' => 0,
        ]);

        $basic->features()->saveMany([
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::COLLECTIONS_LIMIT->slug(),
                'name' => 'Collections',
                'description' => 'up to 5 collection',
                'value' => 5
            ]),
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT->slug(),
                'name' => 'Collection Items',
                'description' => 'Collection items limit',
                'value' => 100
            ]),
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::OPENAI_REQUESTS->slug(),
                'name' => 'OpenAI requests',
                'description' => 'OpenAI requests limit',
                'value' => 10000,
                'resettable_interval' => ResettableIntervalEnum::MONTH->value,
                'resettable_period' => 1,
            ]),
        ]);

        $pro = $createPlan([
            'slug' => SubscriptionPlanEnum::PRO->slug(),
            'name' => 'Pro plan',
            'description' => null,
            'is_active' => true,
            'trial_period' => 14,
            'invoice_period' => 0,
        ]);

        $pro->features()->saveMany([
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::COLLECTIONS_LIMIT->slug(),
                'name' => 'Collections',
                'description' => 'up to 15 collections',
                'value' => 15
            ]),
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT->slug(),
                'name' => 'Collection Items',
                'description' => 'Collection items limit',
                'value' => 200
            ]),
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::OPENAI_REQUESTS->slug(),
                'name' => 'OpenAI requests',
                'description' => 'OpenAI requests limit',
                'value' => 20000,
                'resettable_interval' => ResettableIntervalEnum::MONTH->value,
                'resettable_period' => 1,
            ]),
        ]);

        $enterprise = $createPlan([
            'slug' => SubscriptionPlanEnum::ENTERPRISE->slug(),
            'name' => 'Enterprise plan',
            'description' => null,
            'is_active' => true,
            'trial_period' => 14,
            'invoice_period' => 0,
        ]);

        $enterprise->features()->saveMany([
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::COLLECTIONS_LIMIT->slug(),
                'name' => 'Collections',
                'description' => 'unlimited collections',
                'value' => 'true'
            ]),
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::COLLECTION_ITEMS_LIMIT->slug(),
                'name' => 'Collection Items',
                'description' => 'Collection items limit',
                'value' => 1000
            ]),
            new PlanFeature([
                'slug' => SubscriptionFeatureEnum::OPENAI_REQUESTS->slug(),
                'name' => 'OpenAI requests',
                'description' => 'OpenAI requests limit',
                'value' => 50000,
                'resettable_interval' => ResettableIntervalEnum::MONTH->value,
                'resettable_period' => 1,
            ]),
        ]);
    }
}
