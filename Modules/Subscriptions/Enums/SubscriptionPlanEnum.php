<?php

namespace Modules\Subscriptions\Enums;

enum SubscriptionPlanEnum
{
    case BASE;

    case PRO;

    case ENTERPRISE;

    public function slug(): string
    {
        return match ($this) {
            self::BASE => 'base',
            self::PRO => 'pro',
            self::ENTERPRISE => 'enterprise',
        };
    }
}
