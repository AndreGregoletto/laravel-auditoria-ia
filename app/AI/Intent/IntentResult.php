<?php

namespace App\AI\Intent;

class IntentResult
{
    public function __construct(
        public IntentType $intent,
        public array $filters = [],
        public ?string $focus = null,
        public ?string $raw = null,
        public float $confidence = 0.0,
    ) {}
}
