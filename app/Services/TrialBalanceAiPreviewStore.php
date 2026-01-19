<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TrialBalanceAiPreviewStore
{
    public function key(int $fileId, int $userId): string
    {
        return "ai:trial_balance:file:{$fileId}:user:{$userId}";
    }

    public function put(int $fileId, int $userId, array $payload, int $ttlSeconds = 12000): void
    {
        Cache::store('redis')->put($this->key($fileId, $userId), $payload, $ttlSeconds);
    }

    public function get(int $fileId, int $userId): ?array
    {
        return Cache::store('redis')->get($this->key($fileId, $userId));
    }

    public function forget(int $fileId, int $userId): void
    {
        Cache::store('redis')->forget($this->key($fileId, $userId));
    }
}
