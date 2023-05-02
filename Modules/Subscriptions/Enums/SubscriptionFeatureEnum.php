<?php

namespace Modules\Subscriptions\Enums;

enum SubscriptionFeatureEnum
{
    case COLLECTIONS_LIMIT;

    public function slug(): string
    {
        return match ($this) {
            self::COLLECTIONS_LIMIT => 'collections-limit',
        };
    }
}
