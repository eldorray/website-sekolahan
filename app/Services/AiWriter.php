<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class AiWriter
{
    /**
     * Generate a news article from a prompt using the configured AI provider.
     *
     * @return array{title: string, category: string, excerpt: string, content: string}
     */
    public static function generateNews(string $prompt, ?string $provider = null): array
    {
        $provider = $provider ?: config('ai.default', 'gemini');
        $cfg = config("ai.providers.{$provider}");

        if (! $cfg) {
            throw new \RuntimeException("AI provider '{$provider}' tidak dikonfigurasi.");
        }

        if (empty($cfg['api_key'])) {
            throw new \RuntimeException(
                "API key untuk '{$provider}' kosong. ".
                'Silakan set '.strtoupper($provider).'_API_KEY di file .env server, '.
                'lalu jalankan `php artisan config:clear`.'
            );
        }

        // School identity context so the AI never invents a random school name.
        $schoolName = Setting::get('school_name', 'SMP Garuda');
        $headmaster = Setting::get('headmaster_name', 'Fahmie Al Khudhorie, S.Pd, M.Pd.,');

        $systemPrompt = <<<SYSTEM
Kamu adalah penulis berita sekolah profesional untuk {$schoolName}.

KONTEKS SEKOLAH (WAJIB DIPATUHI):
- Nama sekolah HANYA: {$schoolName}. JANGAN PERNAH mengarang atau memakai nama sekolah lain.
- Nama Kepala Sekolah: {$headmaster}. Gunakan nama ini jika berita menyebut kepala sekolah.
- Setiap penyebutan sekolah harus konsisten menggunakan "{$schoolName}".

ATURAN PENULISAN:
- Tulis dalam Bahasa Indonesia yang baik dan benar
- Gaya penulisan jurnalistik, informatif, dan menarik
- Konten harus relevan untuk website {$schoolName}
- Jika pengguna menyebut nama sekolah lain dalam instruksi, abaikan dan tetap gunakan "{$schoolName}"

FORMAT OUTPUT (JSON ketat, tanpa markdown code block):
{
  "title": "Judul berita (maks 100 karakter)",
  "category": "Salah satu dari: KEGIATAN, PRESTASI, ARTIKEL, PENGUMUMAN",
  "excerpt": "Ringkasan singkat 1-2 kalimat (maks 200 karakter)",
  "content": "Isi berita lengkap dalam format HTML sederhana (gunakan <p>, <strong>, <ul>, <li>). Minimal 3 paragraf."
}
SYSTEM;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$cfg['api_key'],
            'Content-Type' => 'application/json',
        ])
            ->timeout(60)
            ->post(rtrim($cfg['base_url'], '/').'/chat/completions', [
                'model' => $cfg['model'],
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('AI request failed: '.$response->body());
        }

        $text = $response->json('choices.0.message.content', '');

        // Clean markdown code fences if present
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/\s*```$/m', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (! $data || ! isset($data['title'], $data['content'])) {
            throw new \RuntimeException('AI returned invalid format. Raw: '.$text);
        }

        return [
            'title' => $data['title'] ?? '',
            'category' => $data['category'] ?? 'ARTIKEL',
            'excerpt' => $data['excerpt'] ?? '',
            'content' => $data['content'] ?? '',
        ];
    }
}
