<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Jembatan ke gateway AI (format OpenAI-compatible) untuk halaman YPDH AI.
 *
 * Semua pengaturan dibaca dari tabel settings supaya bisa diubah dari admin.
 * API key TIDAK PERNAH dikirim ke browser — halaman publik hanya bicara ke
 * Laravel, Laravel yang memegang key.
 */
class TintaGateway
{
    private const DEFAULT_BASE = 'https://api.kryptonlab.web.id/v1';

    private const DEFAULT_SYSTEM = 'Anda asisten untuk guru di Indonesia. Jawab dalam Bahasa Indonesia yang jelas dan praktis, '
        .'sesuai konteks Kurikulum Merdeka, dan langsung berikan hasil yang siap dipakai di kelas.';

    public static function baseUrl(): string
    {
        return rtrim(trim((string) Setting::get('ypdh_ai_base_url')) ?: self::DEFAULT_BASE, '/');
    }

    public static function key(): string
    {
        return trim((string) Setting::get('ypdh_ai_key'));
    }

    public static function chatModel(): string
    {
        return trim((string) Setting::get('ypdh_ai_model'));
    }

    public static function imageModel(): string
    {
        return trim((string) Setting::get('ypdh_ai_model_image'));
    }

    public static function systemPrompt(): string
    {
        return trim((string) Setting::get('ypdh_ai_system')) ?: self::DEFAULT_SYSTEM;
    }

    /** Chat siap dipakai kalau key dan model chat sudah diisi di admin. */
    public static function chatReady(): bool
    {
        return self::key() !== '' && self::chatModel() !== '';
    }

    /** Tab gambar hanya ditampilkan kalau model gambar diisi. */
    public static function imageReady(): bool
    {
        return self::key() !== '' && self::imageModel() !== '';
    }

    /**
     * Base URL + jalur. Kalau admin sudah menulis jalur lengkap, pakai apa adanya.
     */
    private static function endpoint(string $path): string
    {
        $base = self::baseUrl();

        return preg_match('#/(chat/completions|images/generations)$#', $base) ? $base : $base.$path;
    }

    /**
     * @param  array<int, array{role: string, content: mixed}>  $messages
     */
    public static function chat(array $messages): string
    {
        if (! self::chatReady()) {
            throw new RuntimeException('Pengaturan AI belum lengkap. Admin perlu mengisi API key dan model chat.');
        }

        $response = Http::withToken(self::key())
            ->timeout(180)
            ->acceptJson()
            ->post(self::endpoint('/chat/completions'), [
                'model' => self::chatModel(),
                'temperature' => 0.7,
                'messages' => array_merge(
                    [['role' => 'system', 'content' => self::systemPrompt()]],
                    $messages,
                ),
            ]);

        self::guard($response->status(), $response->body());

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new RuntimeException('Gateway menjawab tanpa isi. Periksa nama model di Admin → Settings.');
        }

        return $content;
    }

    /**
     * @return array<int, string> Daftar URL atau data URI gambar.
     */
    public static function image(string $prompt, int $count, string $size): array
    {
        if (! self::imageReady()) {
            throw new RuntimeException('Model gambar belum diatur. Admin perlu mengisinya di Settings.');
        }

        $response = Http::withToken(self::key())
            ->timeout(180)
            ->acceptJson()
            ->post(self::endpoint('/images/generations'), [
                'model' => self::imageModel(),
                'prompt' => $prompt,
                'n' => $count,
                'size' => $size,
            ]);

        self::guard($response->status(), $response->body());

        $images = [];

        foreach ((array) data_get($response->json(), 'data', []) as $item) {
            if ($b64 = data_get($item, 'b64_json')) {
                $images[] = 'data:image/png;base64,'.$b64;
            } elseif ($url = data_get($item, 'url')) {
                $images[] = (string) $url;
            }
        }

        if ($images === []) {
            throw new RuntimeException('Gateway tidak mengembalikan gambar.');
        }

        return $images;
    }

    /**
     * Pesan galat gateway diteruskan ke guru supaya bisa dilaporkan ke admin,
     * tapi dipotong dan API key-nya disensor — sebagian gateway memantulkan
     * kembali header/kredensial yang diterimanya.
     */
    private static function guard(int $status, string $body): void
    {
        if ($status < 400) {
            return;
        }

        $key = self::key();
        $clean = strip_tags($body);

        if ($key !== '') {
            $clean = str_replace($key, '***', $clean);
        }

        throw new RuntimeException("Gateway menolak (HTTP {$status}): ".mb_substr($clean, 0, 200));
    }
}
