<?php

namespace Modules\Subscriptions\Enums;

enum SubscriptionFeatureEnum
{
    case COLLECTIONS_LIMIT;

    case COLLECTION_ITEMS_LIMIT;

    case OPENAI_REQUESTS;

    case OPENAI_PARAMS;

    public function slug(): string
    {
        return match ($this) {
            self::COLLECTIONS_LIMIT => 'collections-limit',
            self::COLLECTION_ITEMS_LIMIT => 'collection-items-limit',
            self::OPENAI_REQUESTS => 'openai-requests',
            self::OPENAI_PARAMS => 'openai-params'
        };
    }
}
