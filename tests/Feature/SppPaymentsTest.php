<?php

declare(strict_types=1);

use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\User;
use App\Services\SppBilling;
use App\Services\SppPayments;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
    Http::preventStrayRequests();
    Storage::disk('local')->put('spp/qris/school.png', 'test-image');
    Setting::set('spp_qris_image', 'spp/qris/school.png');
});

function sppConfirmationFixture(): array
{
    $guardian = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $student = Student::create([
        'nis' => fake()->unique()->numerify('########'), 'name' => 'Siswa', 'class_name' => 'VII',
        'monthly_fee' => 250000, 'discount_amount' => 50000, 'billing_start' => '2026-01-01', 'is_active' => true,
    ]);
    $student->guardians()->attach($guardian);
    app(SppBilling::class)->generate('2026-02', $student->id);

    return [$student->invoices()->sole(), $guardian];
}

it('allows an active admin to approve once and records the reviewer and paid invoice', function () {
    $payment = sppConfirmation();
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    app(SppPayments::class)->review($payment, $admin, true, 'Cocok dengan mutasi');
    $time = $payment->fresh()->paid_at->toDateTimeString();
    $this->travel(1)->hours();
    app(SppPayments::class)->review($payment, $admin, true, 'Klik ulang');

    expect($payment->fresh()->status)->toBe('approved')
        ->and($payment->fresh()->reviewed_by)->toBe($admin->id)
        ->and($payment->fresh()->review_notes)->toBe('Cocok dengan mutasi')
        ->and($payment->fresh()->reviewed_at)->not->toBeNull()
        ->and($payment->fresh()->paid_at->toDateTimeString())->toBe($time)
        ->and($payment->fresh()->active_invoice_id)->toBeNull()
        ->and($payment->invoice->fresh()->status)->toBe('paid')
        ->and($payment->invoice->fresh()->paid_at->toDateTimeString())->toBe($time)
        ->and(AuditLog::where('event', 'spp.approved')->count())->toBe(1);
});

it('requires a rejection reason and permits a corrected confirmation without losing history', function () {
    $payment = sppConfirmation();
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    expect(fn () => app(SppPayments::class)->review($payment, $admin, false, '  '))->toThrow(ValidationException::class);
    app(SppPayments::class)->review($payment, $admin, false, 'Bukti tidak terbaca');
    expect($payment->fresh()->status)->toBe('rejected')
        ->and($payment->fresh()->active_invoice_id)->toBeNull()
        ->and($payment->invoice->fresh()->status)->toBe('unpaid')
        ->and($payment->fresh()->paid_at)->toBeNull();
    $next = app(SppPayments::class)->confirm($payment->invoice, $payment->payer, 'Perbaikan', UploadedFile::fake()->image('baru.png'));
    expect($next->id)->not->toBe($payment->id)->and($payment->invoice->payments()->count())->toBe(2);
    expect(fn () => app(SppPayments::class)->review($payment, $admin, true))->toThrow(ValidationException::class);
});

it('serves payment proof only to an active admin or linked parent', function () {
    $payment = sppConfirmation();
    $url = '/orang-tua/spp/pembayaran/'.$payment->id.'/bukti';
    $this->get($url)->assertRedirect(route('login'));
    $other = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $this->actingAs($other)->get($url)->assertNotFound();
    $this->actingAs($payment->payer)->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
    $this->actingAs(User::factory()->create(['role' => 'admin']))->get($url)->assertOk();
    $this->actingAs(User::factory()->create(['role' => 'guru']))->get($url)->assertForbidden();
    $payment->payer->update(['is_active' => false]);
    $this->actingAs($payment->payer->fresh())->get($url)->assertForbidden();
});

it('serves the configured QRIS privately to parents and admins without a gateway notification route', function () {
    $url = '/orang-tua/spp/qris';
    $this->get($url)->assertRedirect(route('login'));
    $parent = User::factory()->create(['role' => 'orang_tua']);
    $this->actingAs($parent)->get($url)->assertOk()->assertHeader('Cache-Control', 'max-age=0, no-store, private');
    Storage::disk('local')->delete('spp/qris/school.png');
    $this->actingAs($parent)->get($url)->assertNotFound();
    expect(Route::has('payments.midtrans.notification'))->toBeFalse();
    $this->postJson('/payments/midtrans/notification', ['transaction_status' => 'settlement'])->assertNotFound();
});

it('prevents duplicate confirmation from either guardian without storing another proof', function () {
    $payment = sppConfirmation();
    $second = User::factory()->create(['role' => 'orang_tua']);
    $payment->invoice->student->guardians()->attach($second);
    expect(fn () => app(SppPayments::class)->confirm($payment->invoice, $second, 'Ulang', UploadedFile::fake()->image('dua.png')))->toThrow(ValidationException::class);
    expect($payment->invoice->payments()->count())->toBe(1)
        ->and(Storage::disk('local')->files('spp/proofs'))->toHaveCount(1);
});

it('rechecks guardian privileges and linkage before accepting a proof', function (string $case) {
    [$invoice, $guardian] = sppConfirmationFixture();
    if ($case === 'role') {
        User::whereKey($guardian->id)->update(['role' => 'guru']);
    } elseif ($case === 'inactive') {
        User::whereKey($guardian->id)->update(['is_active' => false]);
    } else {
        $invoice->student->guardians()->detach($guardian);
    }
    try {
        app(SppPayments::class)->confirm($invoice, $guardian, 'SPP', UploadedFile::fake()->image('bukti.png'));
        $this->fail('Unauthorized confirmation accepted');
    } catch (HttpException $exception) {
        expect($exception->getStatusCode())->toBe($case === 'unlinked' ? 404 : 403);
    }
    expect(SppPayment::count())->toBe(0)->and(Storage::disk('local')->files('spp/proofs'))->toBe([]);
})->with(['role', 'inactive', 'unlinked']);

it('blocks confirmation with unavailable QRIS or non-payable invoices', function (string $case) {
    [$invoice, $guardian] = sppConfirmationFixture();
    if ($case === 'qris') {
        Storage::disk('local')->delete('spp/qris/school.png');
    } elseif ($case === 'zero') {
        $invoice->update(['amount' => 0]);
    } else {
        $invoice->update(['status' => $case]);
    }
    expect(fn () => app(SppPayments::class)->confirm($invoice, $guardian, 'SPP', UploadedFile::fake()->image('bukti.png')))->toThrow(ValidationException::class);
    expect(SppPayment::count())->toBe(0)->and(Storage::disk('local')->files('spp/proofs'))->toBe([]);
})->with(['qris', 'zero', 'paid', 'waived', 'refunded']);

it('rejects invalid descriptions and unsafe proof types before writing', function (string $case) {
    [$invoice, $guardian] = sppConfirmationFixture();
    $description = $case === 'blank' ? '   ' : ($case === 'long' ? str_repeat('a', 1001) : 'SPP');
    $proof = match ($case) {
        'pdf' => UploadedFile::fake()->create('bukti.pdf', 10, 'application/pdf'),
        'large' => UploadedFile::fake()->image('bukti.png')->size(5121),
        'svg' => UploadedFile::fake()->createWithContent('bukti.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'),
        default => UploadedFile::fake()->image('bukti.png'),
    };
    expect(fn () => app(SppPayments::class)->confirm($invoice, $guardian, $description, $proof))->toThrow(ValidationException::class);
    expect(SppPayment::count())->toBe(0)->and(Storage::disk('local')->files('spp/proofs'))->toBe([]);
})->with(['blank', 'long', 'pdf', 'large', 'svg']);

it('rejects revoked reviewers and mismatched invoice amounts without credit', function (string $case) {
    $payment = sppConfirmation();
    $admin = User::factory()->create(['role' => 'admin']);
    if ($case === 'amount') {
        $payment->invoice->update(['amount' => 1]);
        expect(fn () => app(SppPayments::class)->review($payment, $admin, true))->toThrow(ValidationException::class);
    } else {
        User::whereKey($admin->id)->update($case === 'role' ? ['role' => 'orang_tua'] : ['is_active' => false]);
        try {
            app(SppPayments::class)->review($payment, $admin, true);
            $this->fail('Unauthorized review accepted');
        } catch (HttpException $exception) {
            expect($exception->getStatusCode())->toBe(403);
        }
    }
    expect($payment->fresh()->status)->toBe('pending_review')->and($payment->invoice->fresh()->status)->toBe('unpaid');
})->with(['amount', 'role', 'inactive']);

it('cleans up the private proof and payment when audit persistence fails', function () {
    [$invoice, $guardian] = sppConfirmationFixture();
    AuditLog::creating(function () {
        throw new RuntimeException('Simulated audit failure');
    });
    try {
        expect(fn () => app(SppPayments::class)->confirm($invoice, $guardian, 'SPP', UploadedFile::fake()->image('bukti.png')))->toThrow(RuntimeException::class, 'Simulated audit failure');
        expect(SppPayment::count())->toBe(0)->and(Storage::disk('local')->files('spp/proofs'))->toBe([]);
    } finally {
        AuditLog::flushEventListeners();
    }
});

it('prints approved QRIS receipts with the description and manual verification source', function () {
    $payment = sppConfirmation();
    app(SppPayments::class)->review($payment, User::factory()->create(['role' => 'admin']), true);
    $this->actingAs($payment->payer)->get(route('orang-tua.spp.receipt', $payment->invoice))
        ->assertOk()->assertSee('QRIS statis')->assertSee('SPP Februari')->assertSee('diverifikasi admin');
});

it('allows rejection of missing proof so parents can send corrected evidence but never approves it', function () {
    $payment = sppConfirmation();
    $admin = User::factory()->create(['role' => 'admin']);
    Storage::disk('local')->delete($payment->proof_path);
    expect(fn () => app(SppPayments::class)->review($payment, $admin, true))->toThrow(ValidationException::class);
    app(SppPayments::class)->review($payment, $admin, false, 'Bukti hilang, mohon unggah ulang');
    expect($payment->fresh()->status)->toBe('rejected')->and($payment->fresh()->active_invoice_id)->toBeNull();
    $next = app(SppPayments::class)->confirm($payment->invoice, $payment->payer, 'Bukti pengganti', UploadedFile::fake()->image('baru.png'));
    expect($next->status)->toBe('pending_review')->and($payment->invoice->fresh()->status)->toBe('unpaid');
});

it('releases an unpaid sandbox legacy order explicitly without erasing its history', function () {
    [$invoice, $guardian] = sppConfirmationFixture();
    $admin = User::factory()->create(['role' => 'admin']);
    $legacy = $invoice->payments()->create(['order_id' => 'LEGACY-TEST', 'environment' => 'sandbox', 'amount' => $invoice->amount, 'status' => 'pending', 'active_invoice_id' => $invoice->id, 'requires_review' => true]);
    $this->artisan('spp:release-legacy', ['payment' => $legacy->id, '--admin' => $admin->id, '--reason' => 'Arsip simulasi, tidak menerima uang nyata'])->assertSuccessful();
    expect($legacy->fresh()->status)->toBe('legacy_closed')->and($legacy->fresh()->active_invoice_id)->toBeNull()
        ->and($legacy->fresh()->reviewed_by)->toBe($admin->id)->and($legacy->fresh()->requires_review)->toBeFalse();
    $next = app(SppPayments::class)->confirm($invoice, $guardian, 'Pembayaran nyata', UploadedFile::fake()->image('bukti.png'));
    expect($next->status)->toBe('pending_review')->and($invoice->payments()->count())->toBe(2);
});

it('never releases unresolved production or paid legacy orders by default', function (string $case) {
    [$invoice] = sppConfirmationFixture();
    $admin = User::factory()->create(['role' => 'admin']);
    $legacy = $invoice->payments()->create(['order_id' => 'LEGACY-PROD', 'environment' => 'production', 'amount' => $invoice->amount, 'status' => $case === 'paid' ? 'paid' : 'pending', 'paid_at' => $case === 'paid' ? now() : null, 'active_invoice_id' => $invoice->id]);
    $arguments = ['payment' => $legacy->id, '--admin' => $admin->id, '--reason' => 'Pemeriksaan operator'];
    if ($case === 'paid') {
        $arguments['--confirmed-closed-unpaid'] = true;
    }
    $this->artisan('spp:release-legacy', $arguments)->assertFailed();
    expect($legacy->fresh()->active_invoice_id)->toBe($invoice->id);
})->with(['unresolved', 'paid']);

it('requires explicit closure attestation to release a production legacy order', function () {
    [$invoice] = sppConfirmationFixture();
    $admin = User::factory()->create(['role' => 'admin']);
    $legacy = $invoice->payments()->create(['order_id' => 'LEGACY-CLOSED', 'environment' => 'production', 'amount' => $invoice->amount, 'status' => 'expire', 'active_invoice_id' => $invoice->id]);
    $this->artisan('spp:release-legacy', ['payment' => $legacy->id, '--admin' => $admin->id, '--reason' => 'Pesanan ditutup dan tidak ada uang masuk setelah pemeriksaan', '--confirmed-closed-unpaid' => true])->assertSuccessful();
    expect($legacy->fresh()->active_invoice_id)->toBeNull()->and($invoice->fresh()->status)->toBe('unpaid');
    $this->assertDatabaseHas('audit_logs', ['event' => 'spp.legacy_closed', 'user_id' => $admin->id]);
});

function sppConfirmation(): SppPayment
{
    [$invoice, $guardian] = sppConfirmationFixture();

    return app(SppPayments::class)->confirm($invoice, $guardian, 'SPP Februari', UploadedFile::fake()->image('bukti.png'));
}

it('records a static QRIS confirmation privately without settling the invoice or calling a gateway', function () {
    [$invoice, $guardian] = sppConfirmationFixture();
    $payment = app(SppPayments::class)->confirm($invoice, $guardian, '  SPP Februari  ', UploadedFile::fake()->image('bukti.png'));

    expect($payment->status)->toBe('pending_review')
        ->and($payment->payment_type)->toBe('qris_static')
        ->and($payment->environment)->toBe('production')
        ->and($payment->description)->toBe('SPP Februari')
        ->and($payment->amount)->toBe(200000)
        ->and($payment->user_id)->toBe($guardian->id)
        ->and($payment->active_invoice_id)->toBe($invoice->id)
        ->and($payment->confirmed_at)->not->toBeNull()
        ->and($payment->paid_at)->toBeNull()
        ->and($invoice->fresh()->status)->toBe('unpaid');
    Storage::disk('local')->assertExists($payment->proof_path);
    expect($payment->proof_path)->toStartWith('spp/proofs/');
    Http::assertNothingSent();
    $this->assertDatabaseHas('audit_logs', ['event' => 'spp.confirmed', 'user_id' => $guardian->id, 'auditable_id' => $payment->id]);
});
