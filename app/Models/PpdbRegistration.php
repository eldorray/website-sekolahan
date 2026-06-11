<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PpdbRegistration extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'registration_number',
        'full_name',
        'nickname',
        'gender',
        'birthplace',
        'birthdate',
        'previous_school',
        'address',
        'father_name',
        'mother_name',
        'parent_phone',
        'parent_email',
        'grade_target',
        'kk_file',
        'birth_certificate_file',
        'status',
        'notes',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    /**
     * Create a registration with a per-year sequential number, safely under
     * concurrent submissions.
     *
     * The previous `count() + 1` approach could hand the same number to two
     * simultaneous requests. Here the next sequence is read under a row lock
     * inside a transaction; the unique index on `registration_number` is the
     * final backstop, and we retry a few times if a race still slips through.
     */
    public static function createWithNumber(array $data): self
    {
        $year = Carbon::now()->format('Y');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return DB::transaction(function () use ($data, $year) {
                    $last = static::query()
                        ->where('registration_number', 'like', "PPDB-{$year}-%")
                        ->lockForUpdate()
                        ->orderByDesc('registration_number')
                        ->value('registration_number');

                    $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
                    $data['registration_number'] = sprintf('PPDB-%s-%04d', $year, $seq);

                    return static::create($data);
                });
            } catch (QueryException $e) {
                // Unique-constraint clash from a concurrent insert — retry.
                if (! str_contains(strtolower($e->getMessage()), 'unique')) {
                    throw $e;
                }
            }
        }

        throw new \RuntimeException('Gagal membuat nomor pendaftaran PPDB. Silakan coba lagi.');
    }
}
