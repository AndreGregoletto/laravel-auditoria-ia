<?php

namespace App\AI\Resolvers;

use App\AI\Intent\IntentResult;

interface ResolverInterface
{
    public function resolve(IntentResult $intent): array;
}
