<?php

namespace App\Models\Concerns;

use PragmaRX\Google2FAQRCode\Google2FA;

/**
 * TOTP-based two-factor authentication for a user. The shared secret and
 * recovery codes are stored encrypted; 2FA only counts as active once the
 * user has confirmed a code (two_factor_confirmed_at).
 */
trait TwoFactorAuthenticatable
{
    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_secret) && ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Begin enrollment: generate a fresh (unconfirmed) secret + recovery codes.
     */
    public function generateTwoFactorSecret(): string
    {
        $secret = app(Google2FA::class)->generateSecretKey();

        $recovery = collect(range(1, 8))
            ->map(fn () => bin2hex(random_bytes(5)).'-'.bin2hex(random_bytes(5)))
            ->all();

        $this->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recovery)),
            'two_factor_confirmed_at' => null,
        ])->save();

        return $secret;
    }

    public function twoFactorSecret(): ?string
    {
        return $this->two_factor_secret ? decrypt($this->two_factor_secret) : null;
    }

    public function twoFactorRecoveryCodes(): array
    {
        return $this->two_factor_recovery_codes
            ? json_decode(decrypt($this->two_factor_recovery_codes), true)
            : [];
    }

    public function confirmTwoFactor(string $code): bool
    {
        if (! $this->two_factor_secret || ! $this->verifyTwoFactorCode($code)) {
            return false;
        }

        $this->forceFill(['two_factor_confirmed_at' => now()])->save();

        return true;
    }

    public function disableTwoFactor(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Verify a TOTP code, or consume a one-time recovery code.
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        $code = trim($code);

        if ($secret = $this->twoFactorSecret()) {
            if (app(Google2FA::class)->verifyKey($secret, $code)) {
                return true;
            }
        }

        $recovery = $this->twoFactorRecoveryCodes();
        if (in_array($code, $recovery, true)) {
            // Consume the used recovery code.
            $remaining = array_values(array_filter($recovery, fn ($c) => $c !== $code));
            $this->forceFill(['two_factor_recovery_codes' => encrypt(json_encode($remaining))])->save();

            return true;
        }

        return false;
    }

    /**
     * Inline QR code (data URI) for the otpauth secret, suitable for an <img>.
     */
    public function twoFactorQrCode(): string
    {
        $google2fa = app(Google2FA::class);

        return $google2fa->getQRCodeInline(
            config('app.name', 'Sekolah'),
            $this->email,
            $this->twoFactorSecret(),
        );
    }
}
