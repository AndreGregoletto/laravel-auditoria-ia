<?php

namespace App\AI\Resolvers;

use App\AI\Intent\IntentType;

class ResolverFactory
{
    public static function make(IntentType $intent): ?ResolverInterface
    {
        return match ($intent) {
            IntentType::SYSTEM_METRIC => app(DashboardResolver::class),
            default => null,
        };
    }
}
