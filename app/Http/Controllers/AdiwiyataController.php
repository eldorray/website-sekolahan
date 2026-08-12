<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AdiwiyataAssessment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Monitoring kelengkapan dokumen Adiwiyata.
 *
 * Halaman ini publik (siapa pun boleh melihat progres), tapi menyimpan
 * penilaian dijaga PIN bersama dari .env. PIN ditukar sekali menjadi flag
 * session; sesudah itu semua penyimpanan lewat session, bukan mengirim PIN
 * berulang kali.
 */
class AdiwiyataController extends Controller
{
    private const SESSION_KEY = 'adiwiyata_unlocked';

    private const THROTTLE_ATTEMPTS = 5;

    private const THROTTLE_DECAY = 300;

    public function index(): View
    {
        $this->ensureEnabled();

        // Belum membuka PIN: tidak ada isi halaman yang dikirim sama sekali,
        // cuma layar kunci.
        if (! $this->unlocked()) {
            return view('adiwiyata-lock', ['pinConfigured' => $this->pin() !== '']);
        }

        return view('adiwiyata', [
            'assessments' => (object) AdiwiyataAssessment::all()
                ->keyBy('folder_key')
                ->map(fn (AdiwiyataAssessment $a): array => $this->present($a))
                ->all(),
        ]);
    }

    /** Snapshot hasil pemindaian Drive. Ikut terkunci — tanpa PIN tidak bisa diambil. */
    public function data(): BinaryFileResponse
    {
        $this->ensureEnabled();
        $this->ensureUnlocked();

        return response()->file(resource_path('data/adiwiyata-tree.json'));
    }

    public function unlock(Request $request): RedirectResponse
    {
        $this->ensureEnabled();

        // Gagal-tertutup: tanpa ADIWIYATA_PIN di .env, tidak ada yang bisa membuka.
        if ($this->pin() === '') {
            abort(404);
        }

        $throttleKey = 'adiwiyata-unlock|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::THROTTLE_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'pin' => __('Terlalu banyak percobaan. Coba lagi dalam :seconds detik.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        $request->validate(['pin' => 'required|string|max:100']);

        if (! hash_equals($this->pin(), (string) $request->input('pin'))) {
            RateLimiter::hit($throttleKey, self::THROTTLE_DECAY);

            throw ValidationException::withMessages(['pin' => __('PIN salah.')]);
        }

        RateLimiter::clear($throttleKey);

        // Ganti ID session saat hak akses naik (cegah session fixation).
        $request->session()->regenerate();
        $request->session()->put(self::SESSION_KEY, true);

        return redirect()->route('adiwiyata');
    }

    public function lock(Request $request): RedirectResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return back();
    }

    public function save(Request $request): JsonResponse
    {
        $this->ensureEnabled();
        $this->ensureUnlocked();

        // ponytail: folder_key tidak dicocokkan ke snapshot. Pemegang PIN adalah
        // staf sekolah, dan baris asing tidak pernah dirender. Cocokkan ke pohon
        // kalau nanti PIN dibagikan lebih luas.
        $data = $request->validate([
            'folder_key' => 'required|string|max:255',
            'status' => 'required|in:ok,partial,empty',
            'note' => 'nullable|string|max:2000',
        ]);

        $assessment = AdiwiyataAssessment::updateOrCreate(
            ['folder_key' => $data['folder_key']],
            ['status' => $data['status'], 'note' => $data['note'] ?: null],
        );

        return response()->json($this->present($assessment));
    }

    public function reset(): JsonResponse
    {
        $this->ensureEnabled();
        $this->ensureUnlocked();

        $deleted = AdiwiyataAssessment::query()->delete();

        return response()->json(['deleted' => $deleted]);
    }

    private function present(AdiwiyataAssessment $a): array
    {
        return [
            'status' => $a->status,
            'note' => $a->note ?? '',
            'savedAt' => $a->updated_at?->toIso8601String(),
        ];
    }

    private function pin(): string
    {
        return (string) config('features.adiwiyata_pin');
    }

    private function unlocked(): bool
    {
        return session(self::SESSION_KEY) === true;
    }

    private function ensureEnabled(): void
    {
        abort_unless(config('features.adiwiyata'), 404);
    }

    private function ensureUnlocked(): void
    {
        abort_unless($this->unlocked(), 403, 'PIN belum dimasukkan.');
    }
}
