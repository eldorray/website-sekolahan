<?php

namespace App\Jobs;

use App\Services\AiWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class GenerateNewsArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Cache TTL for the result, in seconds. */
    public int $ttl = 600;

    /** Allow the AI HTTP call enough time without blocking a web request. */
    public int $timeout = 90;

    public function __construct(
        public string $cacheKey,
        public string $prompt,
        public ?string $provider = null,
    ) {}

    public function handle(): void
    {
        try {
            $result = AiWriter::generateNews($this->prompt, $this->provider);
            Cache::put($this->cacheKey, ['status' => 'done', 'result' => $result], $this->ttl);
        } catch (\Throwable $e) {
            Cache::put($this->cacheKey, ['status' => 'error', 'message' => $e->getMessage()], $this->ttl);
        }
    }

    public function failed(\Throwable $e): void
    {
        Cache::put($this->cacheKey, ['status' => 'error', 'message' => $e->getMessage()], $this->ttl);
    }
}
