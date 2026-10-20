<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DataIndukStudents
{
    public function sync(User $admin, string $url, string $source, int $monthlyFee, string $billingStart): array
    {
        $this->authorize($admin);
        $url = rtrim(trim($url), '/');
        Validator::make(compact('url', 'source', 'monthlyFee', 'billingStart'), [
            'url' => ['required', 'url:http,https', 'max:2000'],
            'source' => ['required', Rule::in(['mi', 'smp'])],
            'monthlyFee' => ['required', 'integer', 'min:0', 'max:100000000'],
            'billingStart' => ['required', 'date_format:Y-m-d'],
        ])->validate();
        $parts = parse_url($url);
        if (isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment'])) {
            throw ValidationException::withMessages(['syncUrl' => 'Gunakan URL utama Data Induk tanpa kredensial, query, atau fragmen.']);
        }
        $this->assertSourceUrl($url);

        try {
            $response = Http::acceptJson()->connectTimeout(5)->timeout(30)->withOptions(['allow_redirects' => false])
                ->get(rtrim($url, '/').'/api/siswa-'.$source.'/all');
        } catch (ConnectionException $exception) {
            throw ValidationException::withMessages(['sync' => 'Tidak dapat menghubungi Data Induk. Periksa URL dan koneksi, lalu coba lagi.']);
        }
        $payload = $response->json();
        if (! $response->successful() || ! is_array($payload) || ($payload['success'] ?? null) !== true
            || ! isset($payload['data']) || ! is_array($payload['data']) || ! array_is_list($payload['data'])
            || ($payload['total'] ?? null) !== count($payload['data'])
            || Validator::make($payload, [
                'data.*' => ['required', 'array'],
                'data.*.id' => ['required', 'integer', 'min:1', 'distinct'],
            ])->fails()) {
            throw ValidationException::withMessages(['sync' => 'Respons Data Induk tidak valid atau server sedang bermasalah. Tidak ada data siswa yang diubah.']);
        }

        $rows = $payload['data'];
        $nisns = collect($rows)->pluck('nisn')->filter(fn ($nisn) => is_string($nisn) && trim($nisn) !== '')
            ->map(fn ($nisn) => trim($nisn))->countBy()->all();

        return DB::transaction(function () use ($admin, $url, $rows, $nisns, $source, $monthlyFee, $billingStart) {
            $this->authorize($admin);
            $this->assertSourceUrl($url);
            Setting::set('data_induk_url', $url);
            Setting::set('data_induk_source', $source);
            $result = ['created' => 0, 'updated' => 0, 'unchanged' => 0, 'skipped' => 0];
            foreach ($rows as $row) {
                if (Validator::make($row, [
                    'nisn' => ['required', 'string', 'max:50'],
                    'nama_lengkap' => ['required', 'string', 'max:160'],
                    'tingkat_rombel' => ['nullable', 'string', 'max:80'],
                    'status' => ['required', Rule::in(['Aktif', 'Tidak Aktif', 'Lulus', 'Pindah', 'Keluar'])],
                ])->fails() || trim($row['nisn']) === '' || Str::squish($row['nama_lengkap']) === '' || ($nisns[trim($row['nisn'])] ?? 0) > 1) {
                    $result['skipped']++;

                    continue;
                }

                $nis = trim($row['nisn']);
                $name = Str::squish($row['nama_lengkap']);
                $student = Student::where('data_induk_source', $source)->where('data_induk_id', $row['id'])->lockForUpdate()->first();
                $existing = Student::where('nis', $nis)->lockForUpdate()->first();
                if ($existing && (! $student || ! $existing->is($student))) {
                    // NISN conflicts must never move another student's billing history.
                    $unlinked = $existing->data_induk_source === null && $existing->data_induk_id === null;
                    $changingUnit = $existing->data_induk_source !== null && $existing->data_induk_source !== $source
                        && ! $existing->is_active && $row['status'] === 'Aktif';
                    if ($student || (! $unlinked && ! $changingUnit) || Str::lower(Str::squish($existing->name)) !== Str::lower($name)) {
                        $result['skipped']++;

                        continue;
                    }
                    $student = $existing;
                }

                $student ??= new Student([
                    'monthly_fee' => $monthlyFee, 'discount_amount' => 0, 'billing_start' => $billingStart,
                ]);
                $student->fill([
                    'nis' => $nis, 'name' => $name, 'class_name' => trim($row['tingkat_rombel'] ?? '') ?: 'Belum diatur',
                    'is_active' => $row['status'] === 'Aktif', 'data_induk_source' => $source, 'data_induk_id' => $row['id'],
                ]);
                $change = ! $student->exists ? 'created' : ($student->isDirty() ? 'updated' : 'unchanged');
                if ($change !== 'unchanged') {
                    $student->save();
                }
                $result[$change]++;
            }

            return $result;
        });
    }

    private function authorize(User $admin): void
    {
        $actor = $admin->fresh();
        abort_unless($actor && $actor->is_active && $actor->isAdmin(), 403);
    }

    private function assertSourceUrl(string $url): void
    {
        $setting = Setting::where('key', 'data_induk_url')->lockForUpdate()->first();
        // Read imports committed while waiting for the settings lock, even under REPEATABLE READ.
        $linkedStudent = Student::whereNotNull('data_induk_id')->lockForUpdate()->first(['id']);
        if ($linkedStudent && $setting?->value !== $url) {
            throw ValidationException::withMessages(['syncUrl' => 'Siswa sudah terhubung ke server Data Induk sebelumnya. Gunakan URL yang sama agar identitas dan riwayat tagihan tidak tertukar.']);
        }
    }
}
