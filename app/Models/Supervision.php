<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['revision', 'teacher_id', 'supervisor_id', 'scheduled_at', 'school_year', 'semester', 'class_name', 'subject', 'status', 'instrument_version', 'instrument', 'observations', 'context', 'pre_observation', 'reflection', 'follow_up', 'follow_up_due', 'follow_up_evidence', 'verification', 'report'])]
class Supervision extends Model
{
    public const STATUSES = ['dijadwalkan' => 'Dijadwalkan', 'observasi' => 'Observasi', 'refleksi' => 'Refleksi guru', 'tindak_lanjut' => 'Tindak lanjut', 'selesai' => 'Selesai'];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'follow_up_due' => 'date', 'instrument' => 'array', 'observations' => 'array'];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function scopeVisibleTo(Builder $query, User $user): void
    {
        if ($user->isGuru()) {
            $query->where('teacher_id', $user->id);
        }
    }

    public function summary(?string $group = null): array
    {
        $sum = $rated = $missing = $irrelevant = 0;
        foreach ($this->instrument as $code => $label) {
            if ($group && ! str_starts_with($code, $group)) {
                continue;
            }
            $rating = (string) ($this->observations[$code]['rating'] ?? '');
            if (in_array($rating, ['1', '2', '3', '4'], true)) {
                $sum += (int) $rating;
                $rated++;
            } elseif ($rating === 'na') {
                $irrelevant++;
            } else {
                $missing++;
            }
        }

        return ['percent' => $rated ? round($sum / ($rated * 4) * 100, 1) : null, 'rated' => $rated, 'missing' => $missing, 'irrelevant' => $irrelevant];
    }
}
