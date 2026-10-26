<?php

use App\Livewire\Supervisions;
use App\Models\Supervision;
use App\Models\User;
use App\Services\SupervisionAi;
use App\Support\SupervisionInstrument;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function supervisionRecord(User $teacher, array $extra = []): Supervision
{
    return Supervision::create(array_merge([
        'teacher_id' => $teacher->id,
        'supervisor_id' => User::factory()->create(['role' => 'kepala_madrasah'])->id,
        'scheduled_at' => '2026-10-27T08:00',
        'school_year' => '2026/2027', 'semester' => '1', 'class_name' => 'IV', 'subject' => 'Fikih',
        'instrument_version' => SupervisionInstrument::VERSION,
        'instrument' => SupervisionInstrument::items(),
        'observations' => [], 'status' => 'dijadwalkan',
    ], $extra));
}

test('only active staff can access supervision', function () {
    $this->get('/supervisi')->assertRedirect('/login');
    foreach (['admin', 'kepala_madrasah', 'guru'] as $role) {
        $this->actingAs(User::factory()->create(['role' => $role, 'is_active' => true]))->get('/supervisi')->assertOk();
    }
    $this->actingAs(User::factory()->create(['role' => 'orang_tua']))->get('/supervisi')->assertForbidden();
    $this->actingAs(User::factory()->create(['role' => 'guru', 'is_active' => false]))->get('/supervisi')->assertForbidden();
});

test('headmaster schedules a supervision with the MI instrument snapshot', function () {
    $head = User::factory()->create(['role' => 'kepala_madrasah']);
    $teacher = User::factory()->create(['role' => 'guru']);
    Livewire::actingAs($head)->test(Supervisions::class)
        ->set('teacherId', $teacher->id)->set('scheduledAt', '2026-10-27T08:00')
        ->set('schoolYear', '2026/2027')->set('className', 'IV')->set('subject', 'Fikih')
        ->call('save')->assertHasNoErrors();
    $record = Supervision::firstOrFail();
    expect($record->teacher_id)->toBe($teacher->id)
        ->and($record->instrument)->toHaveCount(24)
        ->and($record->supervisor_id)->toBe($head->id);
});

test('teachers only see their own records and cannot save supervisor fields', function () {
    $teacher = User::factory()->create(['role' => 'guru']);
    $own = supervisionRecord($teacher);
    $other = supervisionRecord(User::factory()->create(['role' => 'guru']), ['subject' => 'RAHASIA GURU LAIN']);
    $this->actingAs($teacher)->get(route('supervision.print', $other))->assertNotFound();
    $this->get(route('supervision.print', $own))->assertOk();
    Livewire::actingAs($teacher)->test(Supervisions::class)->assertDontSee('RAHASIA GURU LAIN')
        ->call('edit', $own->id)->set('subject', 'Diubah')->call('save')->assertForbidden();
    expect(fn () => Livewire::actingAs($teacher)->test(Supervisions::class)->call('edit', $other->id))->toThrow(ModelNotFoundException::class);
});

test('scores exclude missing and irrelevant observations and expose coverage', function () {
    $record = supervisionRecord(User::factory()->create(['role' => 'guru']), ['observations' => [
        'A1' => ['rating' => '4', 'evidence' => 'Tujuan jelas'],
        'A2' => ['rating' => '2', 'evidence' => 'Sebagian'],
        'A3' => ['rating' => 'na', 'evidence' => 'Alasan'],
        'A4' => ['rating' => 'unobserved', 'evidence' => 'Belum diamati'],
    ]]);
    expect($record->summary()['percent'])->toBe(75.0)
        ->and($record->summary()['rated'])->toBe(2)
        ->and($record->summary()['missing'])->toBe(21);
});

test('AI uses anonymous evidence and validates structured output', function () {
    \App\Models\Setting::set('ypdh_ai_key', 'test');
    \App\Models\Setting::set('ypdh_ai_model', 'test-model');
    Http::fake(['*' => Http::sequence()->push(['choices' => [['message' => ['content' => json_encode(['text' => 'Draf refleksi berdasarkan bukti.'])]]]])->push(['choices' => [['message' => ['content' => '{"text": []}']]]])]);
    $teacher = User::factory()->create(['role' => 'guru', 'name' => 'Nama Rahasia Guru']);
    $record = supervisionRecord($teacher, ['observations' => ['B6' => ['rating' => '2', 'evidence' => 'Hanya dua murid diperiksa.']]]);
    expect(app(SupervisionAi::class)->generate($record, 'reflection'))->toContain('Draf refleksi');
    Http::assertSent(fn ($request) => ! str_contains(json_encode($request->data()), 'Nama Rahasia Guru')
        && str_contains(json_encode($request->data()), 'Hanya dua murid diperiksa.'));
    expect(fn () => app(SupervisionAi::class)->generate($record, 'reflection'))->toThrow(RuntimeException::class);
});

test('teacher reflections and evidence save without changing supervisor assessment', function () {
    $teacher = User::factory()->create(['role' => 'guru']);
    $record = supervisionRecord($teacher);
    Livewire::actingAs($teacher)->test(Supervisions::class)->call('edit', $record->id)
        ->set('reflection', 'Saya perlu memeriksa semua murid.')
        ->set('preObservation', 'Latihan praktik wudu')
        ->set('followUpEvidence', 'Latihan ulang dengan rubrik')
        ->set('status', 'selesai')->set('observations', ['A1' => ['rating' => '4', 'evidence' => 'Palsu']])
        ->call('saveTeacher')->assertHasNoErrors();
    expect($record->fresh()->reflection)->toBe('Saya perlu memeriksa semua murid.')
        ->and($record->fresh()->status)->toBe('dijadwalkan')
        ->and($record->fresh()->observations)->toBe([]);
});

test('ratings save without evidence and completion requires teacher reflection and verification', function () {
    $teacher = User::factory()->create(['role' => 'guru']);
    $record = supervisionRecord($teacher);
    $head = $record->supervisor;
    $component = Livewire::actingAs($head)->test(Supervisions::class)->call('edit', $record->id)
        ->set('observations', ['A1' => ['rating' => '4', 'evidence' => '']])
        ->call('save')->assertHasNoErrors();
    $observations = [];
    foreach ($record->instrument as $code => $label) {
        $observations[$code] = ['rating' => '3', 'evidence' => 'Bukti praktik '.$code];
    }
    $component->set('observations', $observations)->set('status', 'refleksi')->call('save')->assertHasNoErrors();
    $component->set('status', 'selesai')->call('save')->assertHasErrors('status');
    $record->update(['reflection' => 'Refleksi guru', 'follow_up_evidence' => 'Bukti perubahan praktik']);
    $component->call('edit', $record->id)->set('status', 'selesai')
        ->set('followUp', 'Latihan asesmen formatif')->set('followUpDue', '2026-11-01')
        ->set('verification', 'Perubahan sudah diamati')->call('save')->assertHasNoErrors();
    expect($record->fresh()->status)->toBe('selesai');
});

test('AI failure preserves existing draft and print escapes generated content', function () {
    \App\Models\Setting::set('ypdh_ai_key', 'test');
    \App\Models\Setting::set('ypdh_ai_model', 'test-model');
    Http::fake(['*' => Http::response([], 503)]);
    $record = supervisionRecord(User::factory()->create(['role' => 'guru']), [
        'observations' => ['B6' => ['rating' => '2', 'evidence' => 'Dua murid diperiksa']],
        'report' => '<script>alert(1)</script>',
    ]);
    Livewire::actingAs($record->supervisor)->test(Supervisions::class)->call('edit', $record->id)
        ->set('report', 'Draf lama')->call('generateAi', 'report')->assertSet('report', 'Draf lama');
    $this->actingAs($record->supervisor)->get(route('supervision.print', $record))
        ->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;', false);
});

test('AI draft remains editable and requires explicit save', function () {
    \App\Models\Setting::set('ypdh_ai_key', 'test');
    \App\Models\Setting::set('ypdh_ai_model', 'test-model');
    Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => '{"text":"Saran AI"}']]]])]);
    $record = supervisionRecord(User::factory()->create(['role' => 'guru']), [
        'observations' => ['B6' => ['rating' => '2', 'evidence' => 'Dua murid diperiksa']],
    ]);
    Livewire::actingAs($record->supervisor)->test(Supervisions::class)->call('edit', $record->id)
        ->call('generateAi', 'follow_up')->assertSet('followUp', 'Saran AI')
        ->set('followUp', 'Saran yang disunting')->call('save')->assertHasNoErrors();
    expect($record->fresh()->follow_up)->toBe('Saran yang disunting');
});

test('recap respects teacher ownership and period filters', function () {
    $teacher = User::factory()->create(['role' => 'guru']);
    supervisionRecord($teacher, ['subject' => 'FIKIH MILIK SAYA']);
    supervisionRecord($teacher, ['school_year' => '2025/2026', 'subject' => 'PERIODE LAMA']);
    supervisionRecord(User::factory()->create(['role' => 'guru']), ['subject' => 'DATA GURU LAIN']);
    $this->actingAs($teacher)->get(route('supervision.recap', ['year' => '2026/2027', 'semester' => '1']))
        ->assertOk()->assertSee('FIKIH MILIK SAYA')->assertDontSee('PERIODE LAMA')->assertDontSee('DATA GURU LAIN');
});

test('concurrent teacher changes are not overwritten by a stale supervisor form', function () {
    $record = supervisionRecord(User::factory()->create(['role' => 'guru']));
    $component = Livewire::actingAs($record->supervisor)->test(Supervisions::class)->call('edit', $record->id);
    $record->update(['pre_observation' => 'Persiapan terbaru guru', 'revision' => 1]);
    $component->set('subject', 'Perubahan lama')->call('save')->assertHasErrors('conflict');
    expect($record->fresh()->pre_observation)->toBe('Persiapan terbaru guru')
        ->and($record->fresh()->subject)->toBe('Fikih');
});
