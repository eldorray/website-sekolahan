<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

/**
 * Spam protection for public Livewire forms: a honeypot field (with a
 * minimum fill-time check) plus a per-IP rate limit on submissions.
 */
trait WithSpamProtection
{
    use UsesSpamProtection;

    public HoneypotData $extraFields;

    public function mountWithSpamProtection(): void
    {
        $this->extraFields = new HoneypotData;
    }

    /**
     * Run honeypot + rate-limit checks. Call at the start of submit().
     * Throws a ValidationException ('throttle') when rate limited, and
     * aborts 403 (handled by the honeypot package) when the bot trap fires.
     */
    protected function guardAgainstSpam(string $key, int $maxAttempts = 5, int $decaySeconds = 60): void
    {
        $this->protectAgainstSpam();

        $throttleKey = 'form:'.$key.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'throttle' => "Terlalu banyak pengiriman. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        RateLimiter::hit($throttleKey, $decaySeconds);
    }
}
