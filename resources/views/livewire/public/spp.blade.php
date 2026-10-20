<section class="mx-auto max-w-2xl space-y-6 py-6 text-slate-800 dark:text-slate-100 sm:py-10">
    <div class="space-y-3">
        <p class="eyebrow">Pembayaran sekolah</p>
        <h1 class="section-title">Bayar sekolah tanpa login</h1>
        <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">Cari siswa, pilih pembayaran, lalu bayar melalui QRIS sekolah.</p>
    </div>
    @if ($errors->any())
        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200"><ul class="list-inside list-disc space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if ($confirmationReference)
        <div role="status" class="space-y-2 rounded-2xl border border-brand-200 bg-brand-50 p-5 text-sm text-brand-900 dark:border-brand-800 dark:bg-brand-950 dark:text-brand-100">
            <p class="font-bold">Konfirmasi pembayaran diterima.</p><p>Bukti menunggu verifikasi admin. Jangan membayar ulang selama diperiksa.</p><p class="break-all">Referensi: <strong>{{ $confirmationReference }}</strong></p>
        </div>
    @endif
    @unless ($student)
        <form wire:submit="findStudent" class="card space-y-5">
            <h2 class="text-lg font-bold">1. Cari data siswa</h2>
            <div><label for="spp-search" class="label">NISN atau nama lengkap siswa</label><input id="spp-search" type="search" wire:model="search" required minlength="3" maxlength="160" autocomplete="off" class="input min-h-12" placeholder="Masukkan NISN atau nama lengkap" aria-describedby="spp-search-help"><p id="spp-search-help" class="mt-2 text-xs text-slate-600 dark:text-slate-300">Cukup salah satu. Gunakan nama lengkap sesuai data sekolah atau NISN termasuk nol di awal.</p></div>
            <button type="submit" class="btn-brand min-h-12 w-full disabled:opacity-50 sm:w-auto" wire:loading.attr="disabled" wire:target="findStudent"><span wire:loading.remove wire:target="findStudent">Cari siswa</span><span wire:loading wire:target="findStudent" role="status">Mencari…</span></button>
        </form>
        @if ($results->isNotEmpty())
            <div class="space-y-3"><h2 class="text-lg font-bold">Pilih siswa</h2>
                @foreach ($results as $result)
                    <button type="button" wire:key="student-result-{{ $result->id }}" wire:click="selectStudent({{ $result->id }})" class="card flex min-h-16 w-full items-center justify-between gap-3 text-left hover:ring-brand-400"><span><span class="block font-bold">{{ $result->name }}</span><span class="mt-1 block text-sm text-slate-600 dark:text-slate-300">{{ $result->class_name }} @if ($result->data_induk_source) · {{ strtoupper($result->data_induk_source) }} @endif · NISN …{{ substr($result->nis, -4) }}</span></span><span class="text-sm font-semibold text-brand-700 dark:text-brand-300">Pilih →</span></button>
                @endforeach
            </div>
        @endif
    @else
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-brand-50 p-4 dark:bg-brand-950"><div class="min-w-0"><p class="break-words font-bold">{{ $student->name }}</p><p class="text-sm text-slate-600 dark:text-slate-300">{{ $student->class_name }} · NISN …{{ substr($student->nis, -4) }}</p></div><button type="button" wire:click="changeStudent" class="btn-outline min-h-11">Ganti siswa</button></div>
        @unless ($configured)<p role="status" class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-100">QRIS sekolah belum tersedia. Hubungi admin sekolah.</p>@endunless
        @if ($paymentStep === 'qris' && $paymentDetails)
            <div class="card space-y-5">
                <h2 class="text-lg font-bold">3. Bayar melalui QRIS</h2>
                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900"><p class="whitespace-pre-line break-words font-semibold">{{ $paymentDetails['description'] }}</p><p class="mt-2 text-3xl font-bold tabular-nums">Rp {{ number_format($paymentDetails['amount'], 0, ',', '.') }}</p></div>
                <p class="text-sm">Pindai QRIS dengan aplikasi bank atau dompet digital. Masukkan nominal tepat dan periksa nama penerima sebelum membayar.</p>
                <div class="rounded-xl border border-slate-200 bg-white p-3"><img src="{{ route('payments.qris') }}" alt="QRIS pembayaran sekolah" class="mx-auto h-auto max-h-96 w-full object-contain"></div>
                <a href="{{ route('payments.qris') }}" download="qris-sekolah.png" class="btn-outline flex min-h-12 w-full">Simpan / unduh QRIS</a>
                <form wire:submit="confirmPayment" x-data="{ uploading: false }" x-on:livewire-upload-start="uploading = true" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-cancel="uploading = false" class="space-y-4">
                    <div><label for="spp-proof" class="label">Bukti pembayaran</label><input id="spp-proof" type="file" wire:model="proof" accept="image/jpeg,image/png,image/webp" required class="input min-h-12"><p class="mt-1 text-xs text-slate-600 dark:text-slate-300">JPG, PNG, atau WebP, maksimal 5 MB.</p><p wire:loading wire:target="proof" role="status" class="mt-2 text-sm">Mengunggah bukti…</p></div>
                    <button type="submit" class="btn-brand min-h-12 w-full disabled:opacity-50" @disabled(!$proof) x-bind:disabled="uploading || !$wire.proof" wire:loading.attr="disabled" wire:target="proof,confirmPayment"><span wire:loading.remove wire:target="confirmPayment">Konfirmasi pembayaran</span><span wire:loading wire:target="confirmPayment" role="status">Mengirim…</span></button>
                </form>
                <button type="button" wire:click="backToDetails" class="btn-outline min-h-11 w-full">Kembali ke rincian</button>
            </div>
        @else
            <form wire:submit="preparePayment" class="card space-y-5">
                <h2 class="text-lg font-bold">2. Pilih pembayaran</h2>
                <div><label for="payment-category" class="label">Jenis pembayaran</label><select id="payment-category" wire:model.live="category" required class="select min-h-12"><option value="">Pilih pembayaran</option><option value="spp">SPP</option><option value="other">Lainnya</option></select></div>
                @if ($category === 'spp')
                    <div><label for="payment-period" class="label">Bulan SPP</label><input id="payment-period" type="month" wire:model.live="period" required class="input min-h-12"></div>
                    <p class="rounded-xl bg-slate-50 p-4 text-sm dark:bg-slate-900">Tarif SPP: <strong>Rp {{ number_format(max(0, $student->monthly_fee - $student->discount_amount), 0, ',', '.') }}</strong> per bulan.</p>
                @elseif ($category === 'other')
                    <div><label for="payment-description" class="label">Keterangan pembayaran</label><textarea id="payment-description" wire:model="description" required maxlength="1000" rows="3" class="input" placeholder="Contoh: bayar seragam"></textarea></div>
                    <div><label for="payment-amount" class="label">Nominal pembayaran (Rp)</label><input id="payment-amount" type="number" wire:model="amount" min="1" max="100000000" step="1" required class="input min-h-12" placeholder="Contoh: 175000"></div>
                @endif
                <button type="submit" class="btn-brand min-h-12 w-full disabled:opacity-50 sm:w-auto" @disabled(!$configured || !$category) wire:loading.attr="disabled" wire:target="preparePayment">Bayar</button>
            </form>
        @endif
        <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 dark:bg-slate-800/80 dark:ring-slate-700/50" aria-labelledby="student-payment-history-title">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-5 dark:border-slate-700 sm:px-6">
                <div><h2 id="student-payment-history-title" class="text-lg font-bold">Riwayat pembayaran</h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pembayaran terbaru ditampilkan lebih dulu.</p></div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-200">{{ $history->total() }} pembayaran</span>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($history as $payment)
                    <article wire:key="payment-history-{{ $payment->id }}" class="p-5 sm:p-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $payment->category === 'other' ? 'bg-sky-50 text-sky-700 dark:bg-sky-400/10 dark:text-sky-300' : 'bg-brand-50 text-brand-700 dark:bg-brand-400/10 dark:text-brand-300' }}" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">@if ($payment->category === 'other')<path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18v13H3V7Zm0 4h18M7 4h10l4 3H3l4-3Z" />@else<path stroke-linecap="round" stroke-linejoin="round" d="M8 3v4m8-4v4M3 11h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Zm3 10h3" />@endif</svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2"><div><h3 class="text-sm font-bold">{{ $payment->category === 'other' ? 'Lainnya' : 'SPP'.($payment->period ? ' '.$payment->period->translatedFormat('F Y') : '') }}</h3><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ ($payment->confirmed_at ?? $payment->created_at)->translatedFormat('d M Y') }} · {{ ($payment->confirmed_at ?? $payment->created_at)->format('H:i') }}</p></div><p class="w-full text-lg font-bold tabular-nums sm:w-auto">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p></div>
                                @if ($payment->category === 'other')<p class="mt-3 whitespace-pre-line break-words text-sm leading-relaxed text-slate-600 dark:text-slate-300">{{ $payment->description }}</p>@endif
                                <div class="mt-3"><x-payment-status :status="$payment->status" :label="in_array($payment->status, ['approved', 'paid'], true) ? 'Berhasil' : null" /></div>
                                @if ($payment->environment === 'sandbox')<p class="mt-2 text-xs font-semibold text-amber-800 dark:text-amber-300">Simulasi sandbox</p>@endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="px-6 py-10 text-center"><p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat pembayaran siswa ini.</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pembayaran yang dikonfirmasi akan muncul di sini.</p></div>
                @endforelse
            </div>
            @if ($history->hasPages())<div class="border-t border-slate-100 px-5 py-4 dark:border-slate-700 sm:px-6">{{ $history->links() }}</div>@endif
        </section>
    @endunless
    <p class="text-xs leading-relaxed text-slate-600 dark:text-slate-300">Pembayaran dinyatakan berhasil setelah admin memeriksa bukti dan penerimaan dana.</p>
</section>
