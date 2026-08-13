<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\TintaGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;

/**
 * YPDH AI — asisten AI untuk guru.
 *
 * Halaman dikunci PIN seperti Adiwiyata. Panggilan ke gateway AI selalu lewat
 * server: browser tidak pernah menerima API key, dan tiap panggilan dibatasi
 * agar kredit sekolah tidak habis dipakai satu orang (atau bot).
 */
class YpdhAiController extends Controller
{
    private const SESSION_KEY = 'ypdh_ai_unlocked';

    /**
     * Penanda kuota. Sengaja disimpan sebagai isi session, bukan memakai
     * session ID — ID berganti setiap regenerate() dan tidak bisa diandalkan
     * sebagai kunci pembatas.
     */
    private const QUOTA_KEY = 'ypdh_ai_quota_token';

    private const UNLOCK_ATTEMPTS = 5;

    private const UNLOCK_DECAY = 300;

    /** Batas panggilan AI per sesi. Chat mahal, jadi lebih longgar dari gambar. */
    private const CHAT_PER_MINUTE = 10;

    private const IMAGE_PER_MINUTE = 4;

    /** Payload chat termasuk gambar base64; tolak yang kelewat besar sebelum diteruskan. */
    private const MAX_PAYLOAD_BYTES = 6_000_000;

    public function index(): View
    {
        $this->ensureEnabled();

        if (! $this->unlocked()) {
            return view('tool-lock', [
                'icon' => '🖋️',
                'heading' => __('YPDH AI'),
                'action' => route('ypdh-ai.unlock'),
                'pinConfigured' => $this->pin() !== '',
            ]);
        }

        return view('ypdh-ai', [
            'chatReady' => TintaGateway::chatReady(),
            'imageReady' => TintaGateway::imageReady(),
            'model' => TintaGateway::chatModel(),
        ]);
    }

    public function unlock(Request $request): RedirectResponse
    {
        $this->ensureEnabled();

        if ($this->pin() === '') {
            abort(404);
        }

        $throttleKey = 'ypdh-ai-unlock|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::UNLOCK_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'pin' => __('Terlalu banyak percobaan. Coba lagi dalam :seconds detik.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        $request->validate(['pin' => 'required|string|max:100']);

        if (! hash_equals($this->pin(), (string) $request->input('pin'))) {
            RateLimiter::hit($throttleKey, self::UNLOCK_DECAY);

            throw ValidationException::withMessages(['pin' => __('PIN salah.')]);
        }

        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, true);
        $request->session()->put(self::QUOTA_KEY, Str::random(16));

        return redirect()->route('ypdh-ai');
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return back();
    }

    public function chat(Request $request): JsonResponse
    {
        $this->ensureEnabled();
        $this->ensureUnlocked();
        $this->ensureNotTooLarge($request);
        $this->ensureWithinQuota($request, 'chat', self::CHAT_PER_MINUTE);

        $data = $request->validate([
            'messages' => 'required|array|min:1|max:40',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required',
        ]);

        try {
            return response()->json(['content' => TintaGateway::chat($data['messages'])]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }
    }

    public function image(Request $request): JsonResponse
    {
        $this->ensureEnabled();
        $this->ensureUnlocked();
        $this->ensureWithinQuota($request, 'image', self::IMAGE_PER_MINUTE);

        $data = $request->validate([
            'prompt' => 'required|string|max:1000',
            'count' => 'required|integer|min:1|max:4',
            'size' => 'required|in:1024x1024,1024x1792,1792x1024',
        ]);

        try {
            return response()->json(['images' => TintaGateway::image($data['prompt'], $data['count'], $data['size'])]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 502);
        }
    }

    private function ensureWithinQuota(Request $request, string $bucket, int $perMinute): void
    {
        $key = "ypdh-ai-{$bucket}|".($request->session()->get(self::QUOTA_KEY) ?: $request->ip());

        if (RateLimiter::tooManyAttempts($key, $perMinute)) {
            abort(429, __('Terlalu cepat. Tunggu :seconds detik.', [
                'seconds' => RateLimiter::availableIn($key),
            ]));
        }

        RateLimiter::hit($key, 60);
    }

    private function ensureNotTooLarge(Request $request): void
    {
        abort_if(
            strlen((string) $request->getContent()) > self::MAX_PAYLOAD_BYTES,
            413,
            __('Lampiran terlalu besar. Kurangi jumlah atau ukuran berkas.'),
        );
    }

    private function pin(): string
    {
        return (string) config('features.ypdh_ai_pin');
    }

    private function unlocked(): bool
    {
        return session(self::SESSION_KEY) === true;
    }

    private function ensureEnabled(): void
    {
        abort_unless(config('features.ypdh_ai'), 404);
    }

    private function ensureUnlocked(): void
    {
        abort_unless($this->unlocked(), 403, 'PIN belum dimasukkan.');
    }
}
