<?php

namespace Modules\Subscriptions\Enums;

enum SubscriptionFeatureEnum
{
    case COLLECTIONS_LIMIT;

    case COLLECTION_ITEMS_LIMIT;

    case OPENAI_REQUESTS;

    public function slug(): string
    {
        return match ($this) {
            self::COLLECTIONS_LIMIT => 'collections-limit',
            self::COLLECTION_ITEMS_LIMIT => 'collection-items-limit',
            self::OPENAI_REQUESTS => 'openai_requests',
        };
    }
}
