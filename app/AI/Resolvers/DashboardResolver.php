<?php

namespace App\AI\Resolvers;

use App\AI\Intent\IntentResult;
use App\Services\DashboardMetricsService;

class DashboardResolver implements ResolverInterface
{
    public function __construct(
        protected DashboardMetricsService $metrics
    ) {}

    public function resolve(IntentResult $intent): array
    {
        $stats = $this->metrics->getStats();

        return match ($intent->focus) {
            'queue' => $stats['queue'],
            default => $stats,
        };
    }
}
