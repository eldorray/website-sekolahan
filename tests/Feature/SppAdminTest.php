<?php

use App\Livewire\Admin\Spp;
use App\Livewire\Admin\Students;
use App\Models\Setting;
use App\Models\SppInvoice;
use App\Models\SppPayment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Cache::flush();
    Storage::fake('local');
    Storage::fake('public');
});

test('admin configures a real private QRIS image through a native dialog', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    Livewire::actingAs($admin)->test(Spp::class)
        ->assertSee('Atur QRIS')
        ->call('editQris')->assertSet('showQrisModal', true)
        ->set('qrisImage', UploadedFile::fake()->image('merchant-qris.png'))
        ->call('saveQris')->assertHasNoErrors()->assertSet('showQrisModal', false);

    $path = Setting::get('spp_qris_image');
    expect($path)->toStartWith('spp/qris/')->not->toContain('merchant-qris');
    Storage::disk('local')->assertExists($path);
    Storage::disk('public')->assertMissing($path);
});

test('QRIS replacement preserves other settings and deletes only a managed previous image', function (string $oldPath, bool $managed) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Storage::disk('local')->put($oldPath, 'old-image');
    Setting::set('spp_qris_image', $oldPath);
    Setting::set('school_name', 'Sekolah Tetap');

    Livewire::actingAs($admin)->test(Spp::class)->call('editQris')
        ->set('qrisImage', UploadedFile::fake()->image('qris-baru.jpg'))
        ->call('saveQris')->assertHasNoErrors();

    expect(Setting::get('school_name'))->toBe('Sekolah Tetap')
        ->and(Setting::get('spp_qris_image'))->not->toBe($oldPath);
    Storage::disk('local')->assertExists(Setting::get('spp_qris_image'));
    expect(Storage::disk('local')->exists($oldPath))->toBe(! $managed);
})->with([['spp/qris/old-merchant.png', true], ['school/logo.png', false], ['spp/qris/../outside.png', false]]);

test('QRIS rejects invalid images and oversized files without changing the setting', function (UploadedFile $file) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Setting::set('spp_qris_image', 'spp/qris/keep.png');
    Livewire::actingAs($admin)->test(Spp::class)->call('editQris')
        ->set('qrisImage', $file)->call('saveQris')
        ->assertHasErrors(['qrisImage'])->assertSet('showQrisModal', true);
    expect(Setting::get('spp_qris_image'))->toBe('spp/qris/keep.png');
})->with([
    'not an image' => fn () => UploadedFile::fake()->create('fake.png', 10, 'text/plain'),
    'SVG' => fn () => UploadedFile::fake()->create('qris.svg', 10, 'image/svg+xml'),
    'GIF' => fn () => UploadedFile::fake()->image('qris.gif'),
    'too large' => fn () => UploadedFile::fake()->image('qris.png')->size(2049),
]);

test('failed QRIS setting persistence cleans the new upload and preserves the previous image', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Storage::disk('local')->put('spp/qris/keep.png', 'previous-image');
    Setting::set('spp_qris_image', 'spp/qris/keep.png');
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('editQris')
        ->set('qrisImage', UploadedFile::fake()->image('replacement.png'));
    DB::listen(function ($query) {
        if (str_starts_with($query->sql, 'update "settings"')) {
            throw new RuntimeException('QRIS setting write failed');
        }
    });

    expect(fn () => $component->call('saveQris'))->toThrow(RuntimeException::class, 'QRIS setting write failed');
    expect(Setting::query()->where('key', 'spp_qris_image')->value('value'))->toBe('spp/qris/keep.png')
        ->and(Storage::disk('local')->files('spp/qris'))->toBe(['spp/qris/keep.png']);
});

test('failed QRIS storage is rejected before the setting changes', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Setting::set('spp_qris_image', 'spp/qris/keep.png');
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('editQris')
        ->set('qrisImage', UploadedFile::fake()->image('replacement.png'));
    $disk = Mockery::mock(Storage::disk('local'))->makePartial();
    $disk->shouldReceive('put')->once()->andReturn(false);
    Storage::set('local', $disk);

    $component->call('saveQris')->assertHasErrors(['qrisImage'])->assertSet('showQrisModal', true);
    expect(Setting::get('spp_qris_image'))->toBe('spp/qris/keep.png');
});

test('QRIS preview appears only for an existing configured private image', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Setting::set('spp_qris_image', 'spp/qris/merchant.png');
    $component = Livewire::actingAs($admin)->test(Spp::class)
        ->assertDontSee('Lihat QRIS saat ini')->assertViewHas('qrisConfigured', false);
    Storage::disk('local')->put('spp/qris/merchant.png', 'merchant-image');
    $component->call('editQris')->assertViewHas('qrisConfigured', true)
        ->assertSee('Lihat QRIS saat ini')->assertSee(route('spp.qris'), false)
        ->assertSee('QRIS pembayaran sekolah saat ini')
        ->call('closeQrisModal')->assertSet('showQrisModal', false)->assertSet('qrisImage', null);
});

test('student search matches NIS and resets pagination', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    sppAdminStudent(['nis' => 'MATCH-987', 'name' => 'Siswa Dicari']);
    sppAdminStudent(['nis' => 'OTHER-123', 'name' => 'Tidak Cocok']);

    Livewire::actingAs($admin)->test(Students::class)
        ->call('setPage', 3)
        ->set('search', 'MATCH-987')
        ->assertSet('paginators.page', 1)
        ->assertSee('Siswa Dicari')
        ->assertDontSee('Tidak Cocok');
});

test('student management rejects unauthorized actors', function (?string $role, bool $active) {
    if ($role !== null) {
        $this->actingAs(User::factory()->create(['role' => $role, 'is_active' => $active]));
    }

    Livewire::test(Students::class)->assertForbidden();
})->with([[null, true], ['guru', true], ['orang_tua', true], ['admin', false]]);

test('student management rechecks privileges after an admin session is revoked', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $component = Livewire::actingAs($admin)->test(Students::class)->call('create');
    $admin->update(['is_active' => false]);

    $component->call('save')->assertForbidden();
    expect(Student::count())->toBe(0);
});

test('student validation stays in the modal and does not persist invalid tariffs', function ($fee, $discount, string $error) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Livewire::actingAs($admin)->test(Students::class)->call('create')
        ->set('nis', 'INVALID')->set('name', 'Siswa')->set('class_name', 'VII A')
        ->set('billing_start', '2026-07-01')->set('monthly_fee', $fee)->set('discount_amount', $discount)
        ->call('save')->assertHasErrors([$error])->assertSet('showModal', true);
    expect(Student::count())->toBe(0);
})->with([[-1, 0, 'monthly_fee'], [100000001, 0, 'monthly_fee'], ['1.5', 0, 'monthly_fee'], [100, 101, 'discount_amount'], [100, -1, 'discount_amount']]);

test('student links reject staff or inactive parents and prevent partial writes', function (string $role, bool $active) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $invalidGuardian = User::factory()->create(['role' => $role, 'is_active' => $active]);
    Livewire::actingAs($admin)->test(Students::class)->call('create')
        ->set('nis', 'INVALID')->set('name', 'Siswa')->set('class_name', 'VII A')
        ->set('billing_start', '2026-07-01')->set('monthly_fee', 0)->set('discount_amount', 0)
        ->set('guardianIds', [$invalidGuardian->id])->call('save')
        ->assertHasErrors(['guardianIds.0'])->assertSet('showModal', true);
    expect(Student::count())->toBe(0);
})->with([['guru', true], ['admin', true], ['orang_tua', false]]);

test('admin shows payment history without requiring invoice issuance', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation(['name' => 'Siswa Pembayaran']);
    Livewire::actingAs($admin)->test(Spp::class)->assertSee('Riwayat pembayaran siswa')
        ->assertSee('Siswa Pembayaran')->assertDontSee('Terbitkan tagihan SPP')
        ->assertViewHas('items', fn ($items) => $items->sole()->is($payment))
        ->assertViewHas('outstanding', 200000)->assertViewHas('collected', 0);
});

test('payment history filters transaction month status and student while resetting pagination', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $selected = sppAdminConfirmation(['nis' => 'SELECT-123', 'name' => 'Siswa Terpilih']);
    $selected->update(['created_at' => '2026-10-05 12:00:00']);
    $other = sppAdminConfirmation(['name' => 'Siswa Lain']);
    $other->update(['created_at' => '2026-09-05 12:00:00']);
    Livewire::actingAs($admin)->test(Spp::class)->set('month', '2026-10')->call('setPage', 2)
        ->set('search', 'SELECT-123')->assertSet('paginators.page', 1)
        ->assertViewHas('items', fn ($items) => $items->count() === 1 && $items->first()->is($selected))
        ->set('status', 'approved')->assertViewHas('items', fn ($items) => $items->isEmpty())
        ->set('status', 'pending_review')->set('month', '2026-09')->assertViewHas('items', fn ($items) => $items->isEmpty());
});

test('billing shows safe invalid-filter feedback instead of parsing an invalid date', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    Livewire::actingAs($admin)->test(Spp::class)
        ->set('month', '2026-99')->assertSee('Pilih bulan transaksi dan status yang valid.')
        ->assertViewHas('items', fn ($items) => $items->isEmpty());
});

test('editing replaces unavailable guardian links with selectable active parents', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = sppAdminStudent();
    $inactive = User::factory()->create(['role' => 'orang_tua', 'is_active' => false]);
    $student->guardians()->attach($inactive);

    Livewire::actingAs($admin)->test(Students::class)->call('edit', $student->id)
        ->assertSet('guardianIds', [])
        ->assertSee('Tautan akun yang nonaktif atau bukan orang tua akan dilepas saat disimpan.')
        ->call('save')->assertHasNoErrors();
    expect($student->guardians()->count())->toBe(0);
});

test('billing exposes pending manual confirmations across all months without gateway secrets', function () {
    config(['spp.server_key' => 'secret-not-for-browser']);
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation(['name' => 'Siswa Bulan Lama'], ['description' => 'SPP September Aisyah']);

    Livewire::actingAs($admin)->test(Spp::class)->set('month', '2026-10')
        ->assertSee('Menunggu verifikasi')->assertViewHas('pendingCount', 1)
        ->assertViewHas('confirmations', fn ($items) => $items->getPageName() === 'confirmationsPage' && $items->sole()->is($payment))
        ->assertSee('Siswa Bulan Lama')->assertSee('SPP September Aisyah')
        ->assertSee('Periksa pembayaran')->assertSee('QRIS belum dikonfigurasi')
        ->assertDontSee('MIDTRANS')->assertDontSee('Midtrans')->assertDontSee('secret-not-for-browser')
        ->assertViewHas('collected', 0)
        ->call('reviewPayment', $payment->id)
        ->assertSet('showReviewModal', true)->assertSet('reviewingId', $payment->id)
        ->assertSee($payment->payer->name)->assertSee($payment->payer->email)
        ->assertSee('SPP September Aisyah')->assertSee('Rp 200.000')
        ->assertSee(route('spp.payment-proof', $payment), false)
        ->assertDontSee($payment->proof_path);

    expect($payment->invoice->fresh()->status)->toBe('unpaid');
});

test('admin approval records the reviewer and settles only the selected invoice idempotently', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    $other = sppAdminConfirmation();
    $this->travelTo(now()->setDate(2026, 10, 19));

    $component = Livewire::actingAs($admin)->test(Spp::class)->set('month', '')
        ->assertViewHas('collected', 0)->assertViewHas('outstanding', 400000)
        ->call('reviewPayment', $payment->id)->set('reviewNotes', 'Dana sudah masuk rekening sekolah')
        ->call('approvePayment')->assertHasNoErrors()->assertSet('showReviewModal', false)
        ->assertSet('reviewingId', null)->assertViewHas('collected', 200000)
        ->assertViewHas('outstanding', 200000)->assertViewHas('pendingCount', 1);

    expect($payment->fresh()->status)->toBe('approved')
        ->and($payment->fresh()->reviewed_by)->toBe($admin->id)
        ->and($payment->fresh()->review_notes)->toBe('Dana sudah masuk rekening sekolah')
        ->and($payment->fresh()->reviewed_at)->not->toBeNull()
        ->and($payment->invoice->fresh()->status)->toBe('paid')
        ->and($other->fresh()->status)->toBe('pending_review')
        ->and($other->invoice->fresh()->status)->toBe('unpaid');

    $reviewedAt = $payment->fresh()->reviewed_at->toDateTimeString();
    $this->travel(1)->hours();
    $component->call('reviewPayment', $payment->id)->call('approvePayment')->assertHasNoErrors();
    expect($payment->fresh()->reviewed_at->toDateTimeString())->toBe($reviewedAt)
        ->and($payment->invoice->fresh()->status)->toBe('paid');
});

test('admin rejection requires a reason and leaves the invoice unpaid', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('reviewPayment', $payment->id)
        ->set('reviewNotes', '   ')->call('rejectPayment')
        ->assertHasErrors(['reviewNotes'])->assertSet('showReviewModal', true);
    expect($payment->fresh()->status)->toBe('pending_review')
        ->and($payment->invoice->fresh()->status)->toBe('unpaid');

    $component->set('reviewNotes', '  Dana belum masuk rekening sekolah  ')->call('rejectPayment')
        ->assertHasNoErrors()->assertSet('showReviewModal', false)->assertViewHas('pendingCount', 0);
    expect($payment->fresh()->status)->toBe('rejected')
        ->and($payment->fresh()->reviewed_by)->toBe($admin->id)
        ->and($payment->fresh()->review_notes)->toBe('Dana belum masuk rekening sekolah')
        ->and($payment->fresh()->reviewed_at)->not->toBeNull()
        ->and($payment->fresh()->paid_at)->toBeNull()
        ->and($payment->invoice->fresh()->status)->toBe('unpaid');
});

test('admin may approve with no notes after checking the confirmation', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    Livewire::actingAs($admin)->test(Spp::class)->call('reviewPayment', $payment->id)
        ->call('approvePayment')->assertHasNoErrors()->assertSet('showReviewModal', false);
    expect($payment->fresh()->status)->toBe('approved')
        ->and($payment->fresh()->review_notes)->toBeNull()
        ->and($payment->invoice->fresh()->status)->toBe('paid');
});

test('review actions without a selected confirmation do not change invoices', function (string $action) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    Livewire::actingAs($admin)->test(Spp::class)->set('reviewNotes', 'Catatan pemeriksaan')
        ->call($action)->assertHasErrors(['payment']);
    expect($payment->fresh()->status)->toBe('pending_review')
        ->and($payment->invoice->fresh()->status)->toBe('unpaid');
})->with(['approvePayment', 'rejectPayment']);

test('manually invoking approval never settles a legacy gateway payment', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation([], ['payment_type' => 'bank_transfer', 'environment' => 'sandbox']);
    Livewire::actingAs($admin)->test(Spp::class)->call('reviewPayment', $payment->id)
        ->call('approvePayment')->assertHasErrors(['payment']);
    expect($payment->fresh()->status)->toBe('pending_review')
        ->and($payment->invoice->fresh()->status)->toBe('unpaid');
});

test('review notes cannot exceed one thousand characters', function (string $action) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    Livewire::actingAs($admin)->test(Spp::class)->call('reviewPayment', $payment->id)
        ->set('reviewNotes', str_repeat('A', 1001))->call($action)
        ->assertHasErrors(['reviewNotes'])->assertSet('showReviewModal', true)
        ->assertSee('Catatan pemeriksaan maksimal 1.000 karakter.')->assertDontSee('validation.max');
    expect($payment->fresh()->status)->toBe('pending_review')
        ->and($payment->invoice->fresh()->status)->toBe('unpaid');
})->with(['approvePayment', 'rejectPayment']);

test('review identifiers are locked against client retargeting', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    $other = sppAdminConfirmation();
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('reviewPayment', $payment->id);
    expect(fn () => $component->set('reviewingId', $other->id))->toThrow(CannotUpdateLockedPropertyException::class);
});

test('manual review and QRIS actions reauthorize a revoked administrator', function (string $action, string $field, mixed $value) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('editQris')
        ->set('qrisImage', UploadedFile::fake()->image('school.png'))
        ->call('reviewPayment', $payment->id)->set('reviewNotes', 'Dana belum masuk');
    $admin->update([$field => $value]);
    $component->call($action, ...($action === 'reviewPayment' ? [$payment->id] : []))->assertForbidden();
    expect(Setting::query()->where('key', 'spp_qris_image')->exists())->toBeFalse()
        ->and($payment->fresh()->status)->toBe('pending_review')
        ->and($payment->invoice->fresh()->status)->toBe('unpaid');
})->with(['editQris', 'saveQris', 'closeQrisModal', 'reviewPayment', 'approvePayment', 'rejectPayment', 'closeReviewModal'])
    ->with([['role', 'orang_tua'], ['is_active', false]]);

test('legacy and completed transactions remain read only and absent from the pending queue', function (array $attributes) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation([], array_merge([
        'description' => 'Catatan historis pembayaran', 'review_notes' => 'Alasan lama tetap tersimpan',
        'transaction_id' => 'hidden-gateway-reference', 'redirect_url' => 'https://secret.example/private-token',
    ], $attributes));
    Livewire::actingAs($admin)->test(Spp::class)->assertViewHas('pendingCount', 0)
        ->call('reviewPayment', $payment->id)
        ->assertSee('Riwayat hanya-baca')->assertSee('Catatan historis pembayaran')
        ->assertSee('Alasan lama tetap tersimpan')->assertDontSee('Setujui pembayaran')
        ->assertDontSee('Tolak konfirmasi')->assertDontSee('hidden-gateway-reference')->assertDontSee('private-token');
})->with([
    [['status' => 'rejected']], [['status' => 'approved']],
    [['payment_type' => 'bank_transfer']], [['environment' => 'sandbox']],
]);

test('manual review reloads a changed payment instead of approving a stale modal', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $payment = sppAdminConfirmation();
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('reviewPayment', $payment->id);
    $payment->update(['status' => 'rejected', 'review_notes' => 'Ditolak oleh pemeriksa lain']);
    $component->call('approvePayment')->assertHasErrors(['payment'])->assertSet('showReviewModal', true)
        ->assertSee('Ditolak oleh pemeriksa lain')->assertDontSee('Setujui pembayaran');
    expect($payment->invoice->fresh()->status)->toBe('unpaid');
});

test('pending queue pagination is independent from invoice month status and search filters', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    foreach (range(1, 11) as $number) {
        sppAdminConfirmation(['name' => 'Antrean '.$number]);
    }
    Livewire::actingAs($admin)->test(Spp::class)->assertViewHas('pendingCount', 11)
        ->assertViewHas('confirmations', fn ($items) => $items->count() === 10 && $items->total() === 11)
        ->call('setPage', 2, 'confirmationsPage')
        ->set('month', '2027-01')->set('status', 'paid')->set('search', 'Tidak ada siswa')
        ->assertViewHas('items', fn ($items) => $items->isEmpty())
        ->assertViewHas('confirmations', fn ($items) => $items->count() === 1 && $items->currentPage() === 2)
        ->assertSee('Antrean 11')->assertSet('paginators.page', 1);
});

test('reviewing the last confirmation on a page returns to the remaining pending queue', function (string $action) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    foreach (range(1, 11) as $number) {
        $payment = sppAdminConfirmation(['name' => 'Antrean '.$number]);
    }
    Livewire::actingAs($admin)->test(Spp::class)->call('setPage', 2, 'confirmationsPage')
        ->call('reviewPayment', $payment->id)->set('reviewNotes', 'Hasil pemeriksaan')->call($action)
        ->assertHasNoErrors()->assertSet('paginators.confirmationsPage', 1)
        ->assertViewHas('confirmations', fn ($items) => $items->count() === 10);
})->with(['approvePayment', 'rejectPayment']);

function sppAdminConfirmation(array $studentAttributes = [], array $attributes = []): SppPayment
{
    $student = sppAdminStudent($studentAttributes);
    $payer = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $student->guardians()->attach($payer);
    $invoice = $student->invoices()->create([
        'period' => '2026-09-01', 'due_date' => '2026-09-10', 'base_amount' => 250000,
        'discount_amount' => 50000, 'amount' => 200000, 'status' => 'unpaid',
    ]);

    $payment = $invoice->payments()->create(array_merge([
        'user_id' => $payer->id, 'order_id' => 'QRIS-'.fake()->uuid(),
        'environment' => 'production', 'amount' => 200000, 'status' => 'pending_review',
        'payment_type' => 'qris_static', 'description' => 'SPP September',
        'proof_path' => 'spp/proofs/'.fake()->uuid().'.png', 'confirmed_at' => now(),
        'active_invoice_id' => $invoice->id,
    ], $attributes));
    Storage::disk('local')->put($payment->proof_path, 'private-proof');

    return $payment;
}

test('admin forms keep native modal synchronization reactive during validation rerenders', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    foreach ([Students::class, Spp::class] as $component) {
        Livewire::actingAs($admin)->test($component)
            ->assertSee('wire:ignore.self', false)
            ->assertSee('x-effect="if (open)', false)
            ->assertSee('type="submit"', false)
            ->call('create')->call('closeModal')->assertSet('showModal', false);
    }
});

test('billing rejects unauthorized actors', function (?string $role, bool $active) {
    if ($role !== null) {
        $this->actingAs(User::factory()->create(['role' => $role, 'is_active' => $active]));
    }
    Livewire::test(Spp::class)->assertForbidden();
})->with([[null, true], ['guru', true], ['orang_tua', true], ['admin', false]]);

test('billing mutations recheck a demoted admin', function (string $action) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('create');
    $admin->update(['role' => 'orang_tua']);
    $component->call($action)->assertForbidden();
    expect(SppInvoice::count())->toBe(0);
})->with(['create', 'save', 'closeModal']);

test('admin routes enforce permission for both student and billing pages', function (string $role, bool $active) {
    $user = User::factory()->create(['role' => $role, 'is_active' => $active]);
    $this->actingAs($user)->get(route('admin.students'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.spp'))->assertForbidden();
})->with([['guru', true], ['orang_tua', true], ['admin', false]]);

test('active admins can visit both full page components', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $this->actingAs($admin)->get(route('admin.students'))->assertOk()->assertSeeLivewire(Students::class);
    $this->actingAs($admin)->get(route('admin.spp'))->assertOk()->assertSeeLivewire(Spp::class);
});

test('billing modal rejects invalid month or inactive student', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $inactive = sppAdminStudent(['is_active' => false]);
    Livewire::actingAs($admin)->test(Spp::class)->call('create')
        ->set('generatePeriod', '2026-13')->call('save')
        ->assertHasErrors(['generatePeriod'])->assertSet('showModal', true)
        ->set('generatePeriod', '2026-10')->set('studentId', (string) $inactive->id)->call('save')
        ->assertHasErrors(['studentId'])->assertSet('showModal', true);
    expect(SppInvoice::count())->toBe(0);
});

test('student identifiers are locked against client-side record retargeting', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $student = sppAdminStudent();
    $other = sppAdminStudent();
    $component = Livewire::actingAs($admin)->test(Students::class)->call('edit', $student->id);
    expect(fn () => $component->set('editingId', $other->id))
        ->toThrow(CannotUpdateLockedPropertyException::class);
});

test('student validation rejects duplicate NIS invalid dates and overlong profile fields', function (string $field, $value) {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    sppAdminStudent(['nis' => 'DUPLICATE']);
    Livewire::actingAs($admin)->test(Students::class)->call('create')
        ->set('nis', 'NEW-VALID')->set('name', 'Siswa')->set('class_name', 'VII A')
        ->set('billing_start', '2026-07-01')->set($field, $value)->call('save')
        ->assertHasErrors([$field])->assertSet('showModal', true);
    expect(Student::count())->toBe(1);
})->with([
    ['nis', 'DUPLICATE'], ['nis', str_repeat('N', 51)], ['name', str_repeat('N', 161)],
    ['class_name', str_repeat('K', 81)], ['billing_start', '2026-02-30'],
]);

test('student and guardian updates roll back together when guardian synchronization fails', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $old = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $new = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $student = sppAdminStudent();
    $student->guardians()->attach($old);
    $component = Livewire::actingAs($admin)->test(Students::class)->call('edit', $student->id)
        ->set('name', 'Perubahan harus dibatalkan')->set('guardianIds', [$new->id]);
    DB::listen(function ($query) {
        if (str_contains($query->sql, 'insert into "student_user"')) {
            throw new RuntimeException('Simulated pivot write failure');
        }
    });
    expect(fn () => $component->call('save'))->toThrow(RuntimeException::class, 'Simulated pivot write failure');
    expect($student->fresh()->name)->toBe('Siswa Contoh')
        ->and($student->guardians()->pluck('users.id')->all())->toBe([$old->id]);
});

test('billing generates one selected student then all eligible students including full waiver', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $selected = sppAdminStudent();
    $waived = sppAdminStudent(['monthly_fee' => 0, 'discount_amount' => 0]);
    sppAdminStudent(['is_active' => false]);
    sppAdminStudent(['billing_start' => '2027-01-01']);
    $component = Livewire::actingAs($admin)->test(Spp::class)->call('create')
        ->set('generatePeriod', '2026-10')->set('studentId', $selected->id)->call('save')->assertHasNoErrors();
    expect(SppInvoice::count())->toBe(1)->and(SppInvoice::sole()->student_id)->toBe($selected->id);
    $component->call('create')->set('generatePeriod', '2026-10')->call('save')->assertHasNoErrors();
    expect(SppInvoice::count())->toBe(2)
        ->and($waived->invoices()->sole()->status)->toBe('waived');
});

function sppAdminStudent(array $attributes = []): Student
{
    return Student::create(array_merge([
        'nis' => 'NIS-'.fake()->unique()->numerify('######'),
        'name' => 'Siswa Contoh',
        'class_name' => 'VII A',
        'monthly_fee' => 250000,
        'discount_amount' => 50000,
        'billing_start' => '2026-07-01',
        'is_active' => true,
    ], $attributes));
}

test('admin creates a student with per-student tariff and multiple guardian links', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $guardians = User::factory()->count(2)->create(['role' => 'orang_tua', 'is_active' => true]);

    Livewire::actingAs($admin)->test(Students::class)
        ->call('create')
        ->assertSet('showModal', true)
        ->set('nis', '2026001')
        ->set('name', 'Aisyah Rahma')
        ->set('class_name', 'VII A')
        ->set('monthly_fee', 300000)
        ->set('discount_amount', 75000)
        ->set('billing_start', '2026-07-01')
        ->set('guardianIds', $guardians->modelKeys())
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false)
        ->assertSee('Aisyah Rahma');

    $student = Student::where('nis', '2026001')->sole();
    expect($student->monthly_fee)->toBe(300000)
        ->and($student->discount_amount)->toBe(75000)
        ->and($student->guardians()->pluck('users.id')->sort()->values()->all())
        ->toBe($guardians->modelKeys());
});

test('admin edits guardians and deactivates a student without changing invoice snapshots', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $oldGuardian = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $newGuardian = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $student = sppAdminStudent();
    $student->guardians()->attach($oldGuardian);
    $invoice = $student->invoices()->create([
        'period' => '2026-07-01', 'due_date' => '2026-07-10', 'base_amount' => 250000,
        'discount_amount' => 50000, 'amount' => 200000, 'status' => 'unpaid',
    ]);

    Livewire::actingAs($admin)->test(Students::class)
        ->call('edit', $student->id)
        ->assertSet('guardianIds', [$oldGuardian->id])
        ->assertSet('showModal', true)
        ->set('guardianIds', [$newGuardian->id])
        ->set('monthly_fee', 0)
        ->set('discount_amount', 0)
        ->set('is_active', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showModal', false);

    expect($student->fresh()->is_active)->toBeFalse()
        ->and($student->fresh()->monthly_fee)->toBe(0)
        ->and($student->guardians()->pluck('users.id')->all())->toBe([$newGuardian->id])
        ->and($invoice->fresh()->amount)->toBe(200000)
        ->and(Student::count())->toBe(1);
});
