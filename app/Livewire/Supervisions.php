<?php

namespace App\Livewire;

use App\Livewire\Concerns\WithNotifications;
use App\Models\Supervision;
use App\Models\User;
use App\Services\SupervisionAi;
use App\Support\SupervisionInstrument;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class Supervisions extends Component
{
    use WithNotifications, WithPagination;

    #[Locked]
    public ?int $editingId = null;

    #[Locked]
    public int $revision = 0;

    public ?int $teacherId = null;

    public string $scheduledAt = '';

    public string $schoolYear = '';

    public string $semester = '1';

    public string $className = '';

    public string $subject = '';

    public string $status = 'dijadwalkan';

    public string $context = '';

    public string $preObservation = '';

    public array $observations = [];

    public string $reflection = '';

    public string $followUp = '';

    public string $followUpDue = '';

    public string $followUpEvidence = '';

    public string $verification = '';

    public string $report = '';

    public string $yearFilter = '';

    public string $semesterFilter = '';

    public string $statusFilter = '';

    public function boot(): void
    {
        $user = User::find(auth()->id());
        abort_unless($user && $user->is_active && in_array($user->role, ['admin', 'kepala_madrasah', 'guru'], true), 403);
    }

    protected function requireSupervisor(): void
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'kepala_madrasah'], true), 403);
    }

    protected function record(): Supervision
    {
        return Supervision::visibleTo(auth()->user())->findOrFail($this->editingId);
    }

    public function edit(int $id): void
    {
        $record = Supervision::visibleTo(auth()->user())->findOrFail($id);
        $this->resetValidation();
        $this->editingId = $record->id;
        $this->revision = $record->revision;
        $this->teacherId = $record->teacher_id;
        $this->scheduledAt = $record->scheduled_at->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i');
        $this->schoolYear = $record->school_year;
        $this->semester = $record->semester;
        $this->className = $record->class_name;
        $this->subject = $record->subject;
        $this->status = $record->status;
        $this->observations = $record->observations;
        foreach (['context', 'reflection', 'verification', 'report'] as $field) {
            $this->$field = $record->$field ?? '';
        }
        $this->preObservation = $record->pre_observation ?? '';
        $this->followUp = $record->follow_up ?? '';
        $this->followUpDue = $record->follow_up_due?->format('Y-m-d') ?? '';
        $this->followUpEvidence = $record->follow_up_evidence ?? '';
    }

    public function newRecord(): void
    {
        $this->requireSupervisor();
        $this->resetExcept('yearFilter', 'semesterFilter', 'statusFilter');
        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->resetExcept('yearFilter', 'semesterFilter', 'statusFilter');
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->requireSupervisor();
        $record = $this->editingId ? $this->record() : null;
        $instrument = $record?->instrument ?? SupervisionInstrument::items();
        $rules = [
            'teacherId' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'guru')->where('is_active', true)],
            'scheduledAt' => 'required|date', 'schoolYear' => ['required', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => 'required|in:1,2', 'className' => 'required|string|max:60', 'subject' => 'required|string|max:120',
            'status' => ['required', Rule::in(array_keys(Supervision::STATUSES))],
            'context' => 'nullable|string|max:6000', 'preObservation' => 'nullable|string|max:6000',
            'followUp' => 'nullable|string|max:10000', 'followUpDue' => 'nullable|date',
            'verification' => 'nullable|string|max:6000', 'report' => 'nullable|string|max:10000',
            'observations' => ['array', 'array:'.implode(',', array_keys($instrument))],
        ];
        foreach ($instrument as $code => $label) {
            $rules["observations.$code"] = 'sometimes|array:rating,evidence';
            $rules["observations.$code.rating"] = ['nullable', Rule::in(array_keys(SupervisionInstrument::RATINGS))];
            $rules["observations.$code.evidence"] = 'nullable|string|max:2000';
        }
        $this->validate($rules);
        if ($record && $record->teacher_id !== $this->teacherId) {
            $this->addError('teacherId', 'Guru pada supervisi tersimpan tidak dapat diganti.');

            return;
        }
        // Bukti/alasan bersifat opsional: nilai boleh disimpan tanpa bukti.
        $data = [
            'teacher_id' => $this->teacherId,
            'scheduled_at' => CarbonImmutable::parse($this->scheduledAt, 'Asia/Jakarta')->utc(), 'school_year' => $this->schoolYear,
            'semester' => $this->semester, 'class_name' => $this->className, 'subject' => $this->subject,
            'status' => $this->status, 'context' => $this->context, 'pre_observation' => $this->preObservation,
            'observations' => $this->observations, 'follow_up' => $this->followUp,
            'follow_up_due' => $this->followUpDue ?: null, 'verification' => $this->verification, 'report' => $this->report,
        ];
        $candidate = $record ? clone $record : new Supervision(['instrument' => $instrument]);
        $candidate->fill($data);
        if (in_array($this->status, ['refleksi', 'tindak_lanjut', 'selesai'], true) && ($candidate->summary()['missing'] || ! $candidate->summary()['rated'])) {
            $this->addError('status', 'Lengkapi setiap indikator dengan nilai atau alasan tidak relevan sebelum tahap refleksi.');

            return;
        }
        if (in_array($this->status, ['tindak_lanjut', 'selesai'], true) && (! trim($record?->reflection ?? '') || ! trim($this->followUp) || ! $this->followUpDue)) {
            $this->addError('status', 'Tahap tindak lanjut membutuhkan refleksi tersimpan dari guru, rencana, dan tenggat.');

            return;
        }
        if ($this->status === 'selesai' && (! trim($record?->follow_up_evidence ?? '') || ! trim($this->verification))) {
            $this->addError('status', 'Selesaikan setelah guru mengirim bukti tindak lanjut dan kepala memverifikasinya.');

            return;
        }
        if ($record) {
            if (! $this->persist($record, $data)) {
                return;
            }
        } else {
            $record = Supervision::create($data + ['supervisor_id' => auth()->id(), 'instrument_version' => SupervisionInstrument::VERSION, 'instrument' => $instrument]);
        }
        $this->edit($record->id);
        $this->notifySuccess('Supervisi tersimpan. Periksa kembali narasi AI bila bukti berubah.');
    }

    public function saveTeacher(): void
    {
        abort_unless(auth()->user()->isGuru(), 403);
        $record = $this->record();
        abort_if($record->status === 'selesai', 403);
        $this->validate([
            'preObservation' => 'nullable|string|max:6000', 'reflection' => 'nullable|string|max:10000',
            'followUpEvidence' => 'nullable|string|max:6000',
        ]);
        if (! $this->persist($record, ['pre_observation' => $this->preObservation, 'reflection' => $this->reflection, 'follow_up_evidence' => $this->followUpEvidence])) {
            return;
        }
        $this->notifySuccess('Persiapan, refleksi, dan bukti tindak lanjut tersimpan.');
    }

    protected function persist(Supervision $record, array $data): bool
    {
        $record->fill($data);
        $record->revision = $this->revision + 1;
        $changes = $record->getDirty();
        $changes['updated_at'] = now();
        if (! Supervision::whereKey($record->id)->where('revision', $this->revision)->update($changes)) {
            $this->addError('conflict', 'Data telah berubah. Salin draf Anda lalu buka ulang supervisi sebelum menyimpan.');

            return false;
        }
        $this->revision++;

        return true;
    }

    public function generateAi(string $kind): void
    {
        $record = $this->record();
        abort_unless(in_array($kind, ['reflection', 'follow_up', 'report'], true), 403);
        if ($kind === 'reflection') {
            abort_unless(auth()->user()->isGuru() && $record->status !== 'selesai', 403);
        } else {
            $this->requireSupervisor();
        }
        if ($record->revision !== $this->revision) {
            $this->addError('conflict', 'Data telah berubah. Buka ulang supervisi sebelum membuat draf AI.');

            return;
        }
        if (! $record->summary()['rated']) {
            $this->notifyError('Simpan nilai dan bukti observasi sebelum menggunakan AI.');

            return;
        }
        $key = 'supervision-ai:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->notifyError('Batas 5 permintaan AI per menit tercapai. Coba sebentar lagi.');

            return;
        }
        RateLimiter::hit($key, 60);
        try {
            $text = app(SupervisionAi::class)->generate($record, $kind);
            $field = ['reflection' => 'reflection', 'follow_up' => 'followUp', 'report' => 'report'][$kind];
            $this->$field = $text;
            $this->notifySuccess('Draf AI tersedia. Periksa, sunting, lalu simpan.');
        } catch (\Throwable $exception) {
            report($exception);
            $this->notifyError('Draf AI gagal dibuat. Data tetap aman; coba lagi atau isi secara manual.');
        }
    }

    public function updatedYearFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSemesterFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function filterByStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
        $this->resetPage();
    }

    public function render()
    {
        $base = Supervision::visibleTo(auth()->user())
            ->when($this->yearFilter, fn ($q) => $q->where('school_year', $this->yearFilter))
            ->when($this->semesterFilter, fn ($q) => $q->where('semester', $this->semesterFilter));
        $counts = (clone $base)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $query = (clone $base)->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));
        $items = $query->with(['teacher', 'supervisor'])->orderByDesc('scheduled_at')->paginate(10);
        $selected = $this->editingId ? $this->record() : null;
        $teachers = auth()->user()->isGuru() ? collect() : User::where('role', 'guru')->where('is_active', true)->orderBy('name')->get();
        $years = Supervision::visibleTo(auth()->user())->distinct()->orderByDesc('school_year')->pluck('school_year');

        return view('livewire.supervisions', compact('items', 'selected', 'teachers', 'years', 'counts'))
            ->layout('layouts.panel', ['title' => auth()->user()->isGuru() ? 'Supervisi Saya' : 'Supervisi Akademik MI']);
    }
}
