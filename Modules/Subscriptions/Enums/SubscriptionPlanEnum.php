<?php

namespace Modules\Subscriptions\Enums;

enum SubscriptionPlanEnum
{
    case TRIAL;
    
    case BASE;

    case PRO;

    case ENTERPRISE;

    case UNLIMITED;

    public function slug(): string
    {
        return match ($this) {
            self::TRIAL => 'trial',
            self::BASE => 'base',
            self::PRO => 'pro',
            self::ENTERPRISE => 'enterprise',
            self::UNLIMITED => 'unlimited',
        };
    }
}
