<?php

use App\Livewire\Admin\Spp as AdminSpp;
use App\Livewire\Public\Spp;
use App\Models\Setting;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\User;
use App\Services\SppPayments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
    Storage::fake('local');
    Storage::disk('local')->put('spp/qris/school.png', UploadedFile::fake()->image('school.png')->getContent());
    Setting::set('spp_qris_image', 'spp/qris/school.png');
});

function publicPaymentStudent(array $data = []): Student
{
    return Student::create(array_replace([
        'nis' => '0012345678', 'name' => 'Anak Pertama', 'class_name' => 'VII A',
        'monthly_fee' => 250000, 'discount_amount' => 50000, 'billing_start' => '2026-07-01', 'is_active' => true,
    ], $data));
}

function selectPublicStudent(Student $student)
{
    return Livewire::test(Spp::class)->set('search', $student->nis)->call('findStudent')->call('selectStudent', $student->id);
}

test('one search field accepts either exact NISN or full name then requires selecting a result', function (string $search) {
    $student = publicPaymentStudent();
    publicPaymentStudent(['nis' => '0099999999', 'name' => 'Siswa Rahasia']);
    Livewire::test(Spp::class)->set('search', $search)->call('findStudent')->assertHasNoErrors()
        ->assertSee('Anak Pertama')->assertDontSee('Siswa Rahasia')->assertSet('studentId', null)
        ->call('selectStudent', $student->id)->assertSet('studentId', $student->id)
        ->assertSee('Pilih pembayaran')->assertSee('Riwayat pembayaran');
    $this->assertGuest();
})->with(['0012345678', ' anak   PERTAMA ']);

test('same-name results remain distinct and arbitrary students cannot be selected', function () {
    $first = publicPaymentStudent();
    $second = publicPaymentStudent(['nis' => '0000000002', 'class_name' => 'VIII B']);
    $foreign = publicPaymentStudent(['nis' => '0000000003', 'name' => 'Nama Lain']);
    $component = Livewire::test(Spp::class)->set('search', 'Anak Pertama')->call('findStudent')
        ->assertSee('VII A')->assertSee('VIII B')->call('selectStudent', $second->id)
        ->assertSet('studentId', $second->id);
    expect(fn () => $component->set('studentId', $first->id))->toThrow(CannotUpdateLockedPropertyException::class);
    Livewire::test(Spp::class)->set('search', 'Anak Pertama')->call('findStudent')
        ->call('selectStudent', $foreign->id)->assertNotFound();
});

test('SPP month can be paid without issuing any invoice', function () {
    $student = publicPaymentStudent();
    $component = selectPublicStudent($student)->set('category', 'spp')->set('period', '2026-10')
        ->call('preparePayment')->assertHasNoErrors()->assertSet('paymentStep', 'qris')
        ->assertSee('200.000')->assertSee('QRIS pembayaran sekolah');
    expect(SppInvoice::count())->toBe(0);
    $component->set('proof', UploadedFile::fake()->image('bukti.png'))->call('confirmPayment')
        ->assertHasNoErrors()->assertSee('Konfirmasi pembayaran diterima')->assertSee('Menunggu verifikasi');
    $payment = SppPayment::sole();
    expect($payment->student_id)->toBe($student->id)->and($payment->spp_invoice_id)->toBeNull()
        ->and($payment->category)->toBe('spp')->and($payment->period->format('Y-m'))->toBe('2026-10')
        ->and($payment->amount)->toBe(200000)->and($payment->user_id)->toBeNull()
        ->and($payment->status)->toBe('pending_review')->and(SppInvoice::count())->toBe(0);
    Storage::disk('local')->assertExists($payment->proof_path);
});

test('other payment requires a description and positive whole-rupiah amount and does not require a month', function () {
    $student = publicPaymentStudent();
    $component = selectPublicStudent($student)->set('category', 'other')->set('description', '  Bayar seragam  ')
        ->set('amount', 175000)->call('preparePayment')->assertHasNoErrors()->assertSee('175.000')
        ->set('proof', UploadedFile::fake()->image('seragam.png'))->call('confirmPayment')->assertHasNoErrors()
        ->assertSee('Bayar seragam')->assertSee('Lainnya');
    $payment = SppPayment::sole();
    expect($payment->category)->toBe('other')->and($payment->period)->toBeNull()
        ->and($payment->description)->toBe('Bayar seragam')->and($payment->amount)->toBe(175000)
        ->and(SppInvoice::count())->toBe(0);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Livewire::actingAs($admin)->test(AdminSpp::class)->assertSee('Bayar seragam')
        ->call('reviewPayment', $payment->id)->call('approvePayment')->assertHasNoErrors();
    expect($payment->fresh()->status)->toBe('approved')->and($payment->fresh()->paid_at)->not->toBeNull();
});

test('SPP confirmations are unique per student and month while rejected ones can be retried', function () {
    $student = publicPaymentStudent();
    $prepare = fn () => selectPublicStudent($student)->set('category', 'spp')->set('period', '2026-10')
        ->call('preparePayment')->set('proof', UploadedFile::fake()->image('bukti.png'));
    $first = $prepare();
    $second = $prepare();
    $first->call('confirmPayment')->assertHasNoErrors();
    $second->call('confirmPayment')->assertHasErrors('payment');
    expect(SppPayment::count())->toBe(1);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    app(SppPayments::class)->review(SppPayment::sole(), $admin, false, 'Bukti tidak jelas');
    $prepare()->call('confirmPayment')->assertHasNoErrors();
    expect(SppPayment::count())->toBe(2);
    app(SppPayments::class)->review(SppPayment::latest('id')->first(), $admin, true);
    selectPublicStudent($student)->set('category', 'spp')->set('period', '2026-10')
        ->call('preparePayment')->assertHasErrors('payment');
});

test('repeating a confirmation request is idempotent even for other payments', function () {
    $student = publicPaymentStudent();
    $component = selectPublicStudent($student)->set('category', 'other')->set('description', 'Seragam')
        ->set('amount', 125000)->call('preparePayment')->set('proof', UploadedFile::fake()->image('bukti.png'));
    $details = $component->get('paymentDetails');
    $key = $component->get('confirmationKey');
    $component->call('confirmPayment')->assertHasNoErrors();
    $replayed = app(SppPayments::class)->confirmStudent($student, $details, UploadedFile::fake()->image('ulang.png'), $key);
    expect(SppPayment::count())->toBe(1)->and($replayed->id)->toBe(SppPayment::sole()->id)
        ->and(Storage::disk('local')->files('spp/proofs'))->toHaveCount(1);
});

test('history shows only the selected students payments and never exposes private proofs or admin notes', function () {
    $student = publicPaymentStudent();
    $foreign = publicPaymentStudent(['nis' => '0000000002', 'name' => 'Anak Lain']);
    selectPublicStudent($student)->set('category', 'other')->set('description', 'Seragam anak pertama')->set('amount', 100000)
        ->call('preparePayment')->set('proof', UploadedFile::fake()->image('bukti.png'))->call('confirmPayment');
    selectPublicStudent($foreign)->set('category', 'other')->set('description', 'Pembayaran Rahasia')->set('amount', 150000)
        ->call('preparePayment')->set('proof', UploadedFile::fake()->image('lain.png'))->call('confirmPayment');
    $payment = SppPayment::where('student_id', $student->id)->sole();
    $payment->update(['review_notes' => 'Catatan privat admin']);
    selectPublicStudent($student)->assertSee('Seragam anak pertama')->assertDontSee('Pembayaran Rahasia')
        ->assertDontSee('Catatan privat admin')->assertDontSee($payment->proof_path)->assertDontSee('Lihat bukti');
});

test('details and proof are validated before accepting any money confirmation', function (string $case) {
    $student = publicPaymentStudent();
    $component = selectPublicStudent($student)->set('category', 'other')->set('description', 'Seragam')->set('amount', 100000);
    match ($case) {
        'blank' => $component->set('description', '  ')->call('preparePayment')->assertHasErrors('description'),
        'fraction' => $component->set('amount', '1.5')->call('preparePayment')->assertHasErrors('amount'),
        'negative' => $component->set('amount', -1)->call('preparePayment')->assertHasErrors('amount'),
        'category' => $component->set('category', 'invalid')->call('preparePayment')->assertHasErrors('category'),
        'month' => $component->set('category', 'spp')->set('period', '2026-99')->call('preparePayment')->assertHasErrors('period'),
        'proof' => $component->call('preparePayment')->call('confirmPayment')->assertHasErrors('proof'),
        'skip' => $component->call('confirmPayment')->assertHasErrors('payment'),
    };
    expect(SppPayment::count())->toBe(0)->and(SppInvoice::count())->toBe(0);
})->with(['blank', 'fraction', 'negative', 'category', 'month', 'proof', 'skip']);

test('changing details after QRIS or deactivating the student prevents stale confirmation', function (string $change) {
    $student = publicPaymentStudent();
    $component = selectPublicStudent($student)->set('category', 'spp')->set('period', '2026-10')->call('preparePayment')
        ->set('proof', UploadedFile::fake()->image('bukti.png'));
    match ($change) {
        'month' => $component->set('period', '2026-11'),
        'type' => $component->set('category', 'other'),
        'deactivate' => $student->update(['is_active' => false]),
        'price' => $student->update(['monthly_fee' => 300000]),
    };
    $component->call('confirmPayment')->assertHasErrors();
    expect(SppPayment::count())->toBe(0);
})->with(['month', 'type', 'deactivate', 'price']);

test('legacy invoice amounts and paid history are honored by direct SPP payment', function () {
    $student = publicPaymentStudent();
    $invoice = $student->invoices()->create([
        'period' => '2026-10-01', 'due_date' => '2026-10-10', 'base_amount' => 170000,
        'discount_amount' => 0, 'amount' => 170000, 'status' => 'unpaid',
    ]);
    selectPublicStudent($student)->set('category', 'spp')->set('period', '2026-10')->call('preparePayment')
        ->assertSee('170.000')->set('proof', UploadedFile::fake()->image('lama.png'))->call('confirmPayment')->assertHasNoErrors();
    $payment = SppPayment::sole();
    expect($payment->spp_invoice_id)->toBe($invoice->id);
    app(SppPayments::class)->review($payment, User::factory()->create(['role' => 'admin', 'is_active' => true]), true);
    expect($invoice->fresh()->status)->toBe('paid');
    selectPublicStudent($student)->set('category', 'spp')->set('period', '2026-10')->call('preparePayment')->assertHasErrors('payment');
});

test('student search is throttled on Livewire updates and does not return partial-name directories', function () {
    publicPaymentStudent();
    $component = Livewire::test(Spp::class)->set('search', 'Anak');
    foreach (range(1, 10) as $attempt) {
        $component->call('findStudent');
    }
    $component->assertDontSee('Anak Pertama')->set('search', 'Anak Pertama')->call('findStudent')
        ->assertHasErrors('search')->assertSee('Terlalu banyak pencarian');
});
