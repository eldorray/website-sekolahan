<?php

use App\Livewire\Admin\Users;
use App\Livewire\Auth\Login;
use App\Livewire\OrangTua\Spp;
use App\Models\Setting;
use App\Models\SppInvoice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function portalQris(): void
{
    Storage::fake('local');
    Storage::disk('local')->put('spp/qris/school.png', UploadedFile::fake()->image('school.png')->getContent());
    Setting::set('spp_qris_image', 'spp/qris/school.png');
}

function portalInvoice(User $parent, string $name = 'Anak Terhubung'): SppInvoice
{
    $student = Student::create([
        'nis' => (string) Str::uuid(), 'name' => $name, 'class_name' => 'TK A',
        'monthly_fee' => 250000, 'discount_amount' => 0, 'billing_start' => '2026-07-01', 'is_active' => true,
    ]);
    $student->guardians()->attach($parent);

    return SppInvoice::create([
        'student_id' => $student->id, 'period' => '2026-10-01', 'due_date' => '2026-10-10',
        'base_amount' => 250000, 'discount_amount' => 0, 'amount' => 250000, 'status' => 'unpaid',
    ]);
}

test('parent sees only linked children and cannot open payment for other invoices', function () {
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $other = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    portalInvoice($parent);
    $foreign = portalInvoice($other, 'Anak Rahasia');

    Livewire::actingAs($parent)->test(Spp::class)->assertSee('Anak Terhubung')->assertDontSee('Anak Rahasia');
    expect(fn () => Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $foreign->id))
        ->toThrow(ModelNotFoundException::class);
});

test('parent starts payment by choosing a child and can close and reset the details', function () {
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    Livewire::actingAs($parent)->test(Spp::class)
        ->assertSet('showPaymentModal', false)
        ->call('openPayment')
        ->assertSet('showPaymentModal', true)
        ->assertSet('paymentStep', 'details')
        ->assertSet('studentId', '')
        ->assertSet('invoiceId', '')
        ->set('studentId', (string) $invoice->student_id)
        ->set('invoiceId', (string) $invoice->id)
        ->set('description', 'Pembayaran SPP Oktober')
        ->call('closePayment')
        ->assertSet('showPaymentModal', false)
        ->assertSet('description', '')
        ->assertSet('studentId', '')
        ->assertSet('invoiceId', '');
});

test('parent sees static QRIS only after choosing a linked child invoice and description', function () {
    portalQris();
    Http::preventStrayRequests();
    Http::fake();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    Livewire::actingAs($parent)->test(Spp::class)
        ->assertDontSee('alt="QRIS pembayaran SPP"', false)
        ->assertDontSee('src="'.route('spp.qris').'"', false)
        ->call('openPayment')
        ->assertDontSee('alt="QRIS pembayaran SPP"', false)
        ->set('studentId', (string) $invoice->student_id)
        ->set('invoiceId', (string) $invoice->id)
        ->set('description', '  SPP Oktober untuk anak saya  ')
        ->call('preparePayment')
        ->assertHasNoErrors()
        ->assertSet('paymentStep', 'qris')
        ->assertSet('description', 'SPP Oktober untuk anak saya')
        ->assertSee('alt="QRIS pembayaran SPP"', false)
        ->assertSee('src="'.route('spp.qris').'"', false)
        ->assertSee('250.000')
        ->assertSee('Simpan / unduh QRIS')
        ->assertSee('persetujuan admin');
    expect($invoice->payments()->count())->toBe(0);
    Http::assertNothingSent();
});

test('confirmation cannot skip the QRIS step or change the locked step from the client', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    Livewire::actingAs($parent)->test(Spp::class)
        ->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')
        ->call('confirmPayment')
        ->assertHasErrors('payment');

    expect(fn () => Livewire::actingAs($parent)->test(Spp::class)->set('paymentStep', 'qris'))
        ->toThrow(CannotUpdateLockedPropertyException::class);
    expect($invoice->payments()->count())->toBe(0);
});

test('parent confirmation stores proof for manual review without marking invoice paid or making requests', function () {
    portalQris();
    Http::preventStrayRequests();
    Http::fake();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    Livewire::actingAs($parent)->test(Spp::class)
        ->call('openPayment', $invoice->id)
        ->set('description', '  SPP Oktober sudah ditransfer  ')
        ->call('preparePayment')
        ->assertHasNoErrors()
        ->set('proof', UploadedFile::fake()->image('bukti.png'))
        ->call('confirmPayment')
        ->assertHasNoErrors()
        ->assertSet('showPaymentModal', false)
        ->assertSet('paymentStep', 'details')
        ->assertSet('proof', null)
        ->assertSee('Menunggu pemeriksaan admin');

    $payment = $invoice->payments()->sole();
    expect($payment->status)->toBe('pending_review')
        ->and($payment->user_id)->toBe($parent->id)
        ->and($payment->amount)->toBe(250000)
        ->and($payment->description)->toBe('SPP Oktober sudah ditransfer')
        ->and($payment->payment_type)->toBe('qris_static')
        ->and($payment->environment)->toBe('production')
        ->and($payment->confirmed_at)->not->toBeNull()
        ->and($payment->paid_at)->toBeNull()
        ->and($invoice->fresh()->status)->toBe('unpaid');
    Storage::disk('local')->assertExists($payment->proof_path);
    Http::assertNothingSent();
});

test('pending or approved confirmations block preparing another payment', function (string $status) {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $invoice->payments()->create([
        'user_id' => $parent->id, 'order_id' => 'SPP-EXISTING', 'environment' => 'production',
        'amount' => 250000, 'status' => $status, 'payment_type' => 'qris_static',
    ]);

    Livewire::actingAs($parent)->test(Spp::class)
        ->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')
        ->call('preparePayment')
        ->assertHasErrors('payment')
        ->assertSet('paymentStep', 'details')
        ->assertDontSee('alt="QRIS pembayaran SPP"', false);
    expect($invoice->payments()->count())->toBe(1);
})->with(['pending_review', 'approved']);

test('changing the selected child after QRIS requires preparing the details again', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $second = portalInvoice($parent, 'Anak Kedua');

    Livewire::actingAs($parent)->test(Spp::class)
        ->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')
        ->call('preparePayment')
        ->assertSet('paymentStep', 'qris')
        ->set('studentId', (string) $second->student_id)
        ->assertSet('paymentStep', 'details')
        ->assertSet('invoiceId', '')
        ->assertDontSee('alt="QRIS pembayaran SPP"', false)
        ->call('confirmPayment')
        ->assertHasErrors('payment');
    expect($invoice->payments()->count())->toBe(0);
});

test('changing the invoice after QRIS requires preparing the details again', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $second = $invoice->replicate();
    $second->period = '2026-11-01';
    $second->save();

    Livewire::actingAs($parent)->test(Spp::class)
        ->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')
        ->call('preparePayment')
        ->set('invoiceId', (string) $second->id)
        ->assertSet('paymentStep', 'details')
        ->assertDontSee('alt="QRIS pembayaran SPP"', false)
        ->call('confirmPayment')
        ->assertHasErrors('payment');
    expect($invoice->payments()->count())->toBe(0);
});

test('legacy active payments require contacting admin instead of paying again', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $invoice->payments()->create([
        'user_id' => $parent->id, 'order_id' => 'SPP-LEGACY', 'environment' => 'production',
        'amount' => 250000, 'status' => 'pending', 'payment_type' => 'qris', 'active_invoice_id' => $invoice->id,
    ]);

    Livewire::actingAs($parent)->test(Spp::class)
        ->assertSee('Hubungi admin sekolah')
        ->assertDontSee('wire:click="openPayment('.$invoice->id.')"', false)
        ->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')
        ->call('preparePayment')
        ->assertHasErrors('payment')
        ->assertSet('paymentStep', 'details');
});

test('payment details require a linked child and an unpaid invoice belonging to that child', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $other = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $sibling = portalInvoice($parent, 'Anak Kedua');
    $foreign = portalInvoice($other, 'Anak Rahasia');

    $component = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment')
        ->call('preparePayment')->assertHasErrors(['studentId', 'invoiceId', 'description']);
    $component->set('studentId', (string) $foreign->student_id)
        ->set('invoiceId', (string) $foreign->id)
        ->set('description', 'SPP Oktober')
        ->call('preparePayment')->assertHasErrors('studentId');
    $component->set('studentId', (string) $invoice->student_id)
        ->set('invoiceId', (string) $sibling->id)
        ->call('preparePayment')->assertHasErrors('invoiceId');
    $component->set('invoiceId', (string) $foreign->id)
        ->call('preparePayment')->assertHasErrors('invoiceId');
    $invoice->update(['status' => 'paid']);
    $component->set('invoiceId', (string) $invoice->id)
        ->call('preparePayment')->assertHasErrors('invoiceId');
    $component->assertSet('paymentStep', 'details')
        ->assertDontSee('Anak Rahasia')->assertDontSee('alt="QRIS pembayaran SPP"', false);
});

test('description is required after trimming and is limited to one thousand characters', function (string $description) {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', $description)->call('preparePayment')
        ->assertHasErrors('description')->assertSet('paymentStep', 'details')
        ->assertDontSee('alt="QRIS pembayaran SPP"', false);
})->with(['empty' => '', 'whitespace' => " \n\t ", 'too long' => str_repeat('a', 1001)]);

test('missing QRIS setup keeps payment in details and creates no confirmation', function () {
    Storage::fake('local');
    Setting::set('spp_qris_image', null);
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    Livewire::actingAs($parent)->test(Spp::class)->assertSee('QRIS sekolah belum tersedia')
        ->call('openPayment', $invoice->id)->set('description', 'SPP Oktober')
        ->call('preparePayment')->assertHasErrors('payment')
        ->assertSet('paymentStep', 'details')->assertDontSee('alt="QRIS pembayaran SPP"', false);
    expect($invoice->payments()->count())->toBe(0);
});

test('confirmation requires an image proof within the size limit', function (string $kind) {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $proof = match ($kind) {
        'pdf' => UploadedFile::fake()->create('bukti.pdf', 100, 'application/pdf'),
        'gif' => UploadedFile::fake()->image('bukti.gif'),
        'oversize' => UploadedFile::fake()->image('bukti.png')->size(5121),
        default => null,
    };

    $component = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')->call('preparePayment')->assertHasNoErrors();
    if ($proof !== null) {
        $component->set('proof', $proof)->assertSet('paymentStep', 'qris');
    }
    $component->call('confirmPayment')->assertHasErrors('proof')
        ->assertSet('showPaymentModal', true)->assertSet('paymentStep', 'qris');
    expect($invoice->payments()->count())->toBe(0);
})->with(['missing', 'pdf', 'gif', 'oversize']);

test('payment validation displays actionable Indonesian messages', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);

    $component = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment')
        ->call('preparePayment')->assertSee('Pilih anak yang terhubung dengan akun Anda.')
        ->assertSee('Pilih tagihan SPP yang belum lunas.')
        ->assertSee('Keterangan pembayaran wajib diisi.')
        ->assertDontSee('validation.required');
    $component->set('studentId', (string) $invoice->student_id)->set('invoiceId', (string) $invoice->id)
        ->set('description', 'SPP Oktober')->call('preparePayment')->call('confirmPayment')
        ->assertSee('Unggah bukti pembayaran sebelum mengirim konfirmasi.')
        ->assertDontSee('validation.required');
});

test('confirmation submit stays disabled until a proof has finished uploading', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $component = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')->call('preparePayment');
    $buttonDisabled = function (string $html): bool {
        $document = new DOMDocument;
        @$document->loadHTML($html);
        $button = (new DOMXPath($document))->query('//form[@*[name()="wire:submit" and .="confirmPayment"]]//button[@type="submit"]')->item(0);

        return $button?->hasAttribute('disabled') ?? false;
    };

    expect($buttonDisabled($component->html()))->toBeTrue();
    $component->assertSee('x-on:livewire-upload-start="uploading = true"', false)
        ->set('proof', UploadedFile::fake()->image('bukti.png'))
        ->assertSet('paymentStep', 'qris');
    expect($buttonDisabled($component->html()))->toBeFalse();
});

test('a second prepared confirmation is refused after the first one is submitted', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $first = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', 'Pembayaran pertama')->call('preparePayment');
    $second = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', 'Pembayaran ganda')->call('preparePayment');

    $first->set('proof', UploadedFile::fake()->image('pertama.png'))->call('confirmPayment')->assertHasNoErrors();
    $second->set('proof', UploadedFile::fake()->image('kedua.png'))->call('confirmPayment')->assertHasErrors('payment');
    expect($invoice->payments()->count())->toBe(1)->and($invoice->fresh()->status)->toBe('unpaid');
});

test('rejected confirmation shows the reason and permits a new submission', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $invoice->payments()->create([
        'user_id' => $parent->id, 'order_id' => 'SPP-REJECTED', 'environment' => 'production',
        'amount' => 250000, 'status' => 'rejected', 'payment_type' => 'qris_static', 'review_notes' => 'Bukti tidak terbaca',
    ]);

    Livewire::actingAs($parent)->test(Spp::class)->assertSee('Bukti tidak terbaca')->assertSee('Kirim ulang pembayaran')
        ->call('openPayment', $invoice->id)->set('description', 'Bukti yang lebih jelas')
        ->call('preparePayment')->assertHasNoErrors()->set('proof', UploadedFile::fake()->image('jelas.png'))
        ->call('confirmPayment')->assertHasNoErrors();
    expect($invoice->payments()->count())->toBe(2)
        ->and($invoice->payments()->latest('id')->first()->status)->toBe('pending_review');
});

test('confirmation rechecks the guardian account after QRIS was prepared', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $component = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')->call('preparePayment')->set('proof', UploadedFile::fake()->image('bukti.png'));
    User::whereKey($parent->id)->update(['is_active' => false]);

    $component->call('confirmPayment')->assertForbidden();
    expect($invoice->payments()->count())->toBe(0);
});

test('confirmation rechecks child ownership after QRIS was prepared', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $component = Livewire::actingAs($parent)->test(Spp::class)->call('openPayment', $invoice->id)
        ->set('description', 'SPP Oktober')->call('preparePayment')->set('proof', UploadedFile::fake()->image('bukti.png'));
    $invoice->student->guardians()->detach($parent);

    $component->call('confirmPayment')->assertHasErrors('studentId')->assertDontSee('alt="QRIS pembayaran SPP"', false);
    expect($invoice->payments()->count())->toBe(0);
});

test('choosing an invoice is not limited by the current portal pagination page', function () {
    portalQris();
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    foreach (range(1, 12) as $month) {
        $next = $invoice->replicate();
        $next->period = $invoice->period->copy()->addMonths($month);
        $next->save();
    }

    Livewire::actingAs($parent)->test(Spp::class)->call('setPage', 2)
        ->call('openPayment')->set('studentId', (string) $invoice->student_id)
        ->assertViewHas('paymentInvoices', fn ($invoices) => $invoices->count() === 13);
});

test('receipt requires a linked active parent and a paid invoice', function () {
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $other = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $url = '/orang-tua/spp/'.$invoice->id.'/kuitansi';
    $this->actingAs($parent)->get($url)->assertNotFound();
    $invoice->update(['status' => 'paid', 'paid_at' => now()]);
    $invoice->payments()->create([
        'user_id' => $parent->id, 'order_id' => 'SPP-RECEIPT', 'environment' => 'production',
        'amount' => 250000, 'status' => 'approved', 'payment_type' => 'qris_static', 'paid_at' => now(),
    ]);
    $this->actingAs($other)->get($url)->assertNotFound();
    $this->actingAs($parent)->get($url)->assertOk()->assertDontSee('SIMULASI SANDBOX')->assertSee('250.000')->assertSee('diverifikasi admin')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $parent->update(['is_active' => false]);
    $this->actingAs($parent)->get($url)->assertForbidden();
});

test('legacy paid sandbox receipts retain their simulation warning', function () {
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $invoice->update(['status' => 'paid', 'paid_at' => now()]);
    $invoice->payments()->create([
        'user_id' => $parent->id, 'order_id' => 'SPP-LEGACY-RECEIPT', 'environment' => 'sandbox',
        'amount' => 250000, 'status' => 'paid', 'payment_type' => 'qris', 'paid_at' => now(),
    ]);

    $this->actingAs($parent)->get('/orang-tua/spp/'.$invoice->id.'/kuitansi')
        ->assertOk()->assertSee('SIMULASI SANDBOX')->assertSee('250.000');
});

test('parent portal requires authentication', function () {
    $this->get('/orang-tua/spp')->assertRedirect(route('login'));
});

test('guardian is forbidden from staff and financial administration routes', function () {
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    foreach (['/admin', '/admin/students', '/admin/spp', '/admin/users', '/guru'] as $path) {
        $this->actingAs($parent)->get($path)->assertForbidden();
    }
});

test('guardian cannot keep paying after being deactivated during an open session', function () {
    $parent = User::factory()->create(['role' => 'orang_tua', 'is_active' => true]);
    $invoice = portalInvoice($parent);
    $component = Livewire::actingAs($parent)->test(Spp::class);
    User::whereKey($parent->id)->update(['is_active' => false]);
    $component->call('openPayment', $invoice->id)->assertForbidden();
    expect($invoice->payments()->count())->toBe(0);
});

test('teacher cannot enter parent portal', function () {
    $this->actingAs(User::factory()->create(['role' => 'guru']))
        ->get('/orang-tua/spp')->assertForbidden();
});

test('parent login redirects to tuition instead of teacher panel', function () {
    User::factory()->create(['email' => 'parent@example.test', 'role' => 'orang_tua', 'is_active' => true]);

    $this->get('/login')->assertOk();
    Livewire::test(Login::class)
        ->set('email', 'parent@example.test')
        ->set('password', 'password')
        ->call('submit')
        ->assertRedirect('/orang-tua/spp');
});

test('parent cannot provision accounts through direct Livewire calls', function () {
    Livewire::actingAs(User::factory()->create(['role' => 'orang_tua', 'is_active' => true]))
        ->test(Users::class)->assertForbidden();
});

test('admin can provision a guardian login', function () {
    Livewire::actingAs(User::factory()->create(['role' => 'admin']))
        ->test(Users::class)
        ->set('name', 'Orang Tua Siswa')
        ->set('email', 'wali@example.test')
        ->set('password', 'safe-test-password')
        ->set('role', 'orang_tua')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', ['email' => 'wali@example.test', 'role' => 'orang_tua']);
});
