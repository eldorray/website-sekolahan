<div class="space-y-7 text-slate-800 dark:text-slate-100">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">Keuangan sekolah</p>
            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">Pembayaran siswa</h1>
            <p class="mt-2 max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-300">Periksa pembayaran masuk dan telusuri riwayat SPP maupun pembayaran lainnya.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-2 text-xs font-medium {{ $qrisConfigured ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-800 dark:text-amber-300' }}"><span class="h-2 w-2 rounded-full bg-current" aria-hidden="true"></span>{{ $qrisConfigured ? 'QRIS sekolah tersedia.' : 'QRIS belum dikonfigurasi.' }}</span>
            <button type="button" wire:click="editQris" class="btn-secondary min-h-11">Atur QRIS</button>
            @if ($qrisConfigured)<a href="{{ route('spp.qris') }}" target="_blank" rel="noopener" class="text-xs font-semibold text-brand-700 underline underline-offset-4 dark:text-brand-300">Lihat QRIS saat ini</a>@endif
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="card flex items-start justify-between gap-3 !p-5 sm:!p-6">
            <div><p class="text-sm font-medium text-slate-600 dark:text-slate-300">Pembayaran berhasil</p><p class="mt-3 text-2xl font-bold tracking-tight tabular-nums sm:text-3xl">Rp {{ number_format($collected, 0, ',', '.') }}</p><p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Dana yang telah diverifikasi</p></div>
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span>
        </div>
        <div class="card flex items-start justify-between gap-3 !p-5 sm:!p-6">
            <div><p class="text-sm font-medium text-slate-600 dark:text-slate-300">Nominal menunggu verifikasi</p><p class="mt-3 text-2xl font-bold tracking-tight tabular-nums sm:text-3xl">Rp {{ number_format($outstanding, 0, ',', '.') }}</p><p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Bukti pembayaran belum diperiksa</p></div>
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300" aria-hidden="true"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span>
        </div>
    </div>

    <section class="card space-y-5 !p-5 sm:!p-6" aria-labelledby="pending-payments-title">
        <div class="flex flex-wrap items-start justify-between gap-2">
            <div><h2 id="pending-payments-title" class="flex items-center gap-2 text-lg font-bold">Menunggu verifikasi <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-400/10 dark:text-amber-300">{{ $pendingCount }}</span></h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Antrean seluruh bulan, termasuk konfirmasi lama.</p></div>
        </div>
        <div class="space-y-3">
            @forelse ($confirmations as $confirmation)
                <article wire:key="confirmation-{{ $confirmation->id }}" class="flex flex-col gap-4 rounded-xl border border-slate-200 border-l-4 border-l-amber-400 p-4 dark:border-slate-700 dark:border-l-amber-400 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1"><p class="break-words font-semibold">{{ $confirmation->student?->name ?? 'Siswa tidak tersedia' }}</p><p class="mt-1 break-words text-sm text-slate-600 dark:text-slate-300">{{ $confirmation->description }}</p><p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $confirmation->student?->class_name }} · {{ $confirmation->payer?->name ?? 'Tanpa akun' }} · {{ ($confirmation->confirmed_at ?? $confirmation->created_at)->format('d M Y, H:i') }}</p></div>
                    <div class="flex shrink-0 flex-wrap items-center justify-between gap-3 sm:gap-5"><p class="font-bold tabular-nums">Rp {{ number_format($confirmation->amount, 0, ',', '.') }}</p><button type="button" wire:click="reviewPayment({{ $confirmation->id }})" class="btn-primary min-h-11 justify-center">Periksa pembayaran</button></div>
                </article>
            @empty
                <div class="rounded-xl bg-slate-50 px-5 py-6 text-center dark:bg-slate-900/50"><p class="text-sm font-medium text-slate-600 dark:text-slate-300">Tidak ada konfirmasi menunggu verifikasi.</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Pembayaran baru akan muncul di sini setelah orang tua mengirim bukti.</p></div>
            @endforelse
        </div>
        {{ $confirmations->links() }}
    </section>

    <section class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-100 dark:bg-slate-800/80 dark:ring-slate-700/50" aria-labelledby="payment-history-title">
        <div class="border-b border-slate-100 p-5 dark:border-slate-700 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><h2 id="payment-history-title" class="text-lg font-bold">Riwayat pembayaran siswa</h2><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">SPP dan pembayaran lainnya, dari yang terbaru.</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-200">{{ number_format($items->total(), 0, ',', '.') }} pembayaran</span></div>
            <div class="mt-5 grid items-start gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2"><label for="payment-history-search" class="label">Cari siswa</label><div class="relative"><svg class="pointer-events-none absolute left-3.5 top-3.5 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="m21 21-4.5-4.5m2-6a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z" /></svg><input id="payment-history-search" type="search" wire:model.live.debounce.300ms="search" class="input min-h-11 !pl-10" placeholder="Nama, NISN, atau kelas"></div></div>
                <div><label for="payment-history-month" class="label">Bulan transaksi</label><input id="payment-history-month" type="month" wire:model.live="month" class="input min-h-11"><p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Kosongkan untuk semua bulan.</p></div>
                <div><label for="payment-history-status" class="label">Status</label><select id="payment-history-status" wire:model.live="status" class="select min-h-11"><option value="">Semua status</option><option value="pending_review">Menunggu verifikasi</option><option value="approved">Disetujui</option><option value="rejected">Ditolak</option><option value="paid">Lunas (arsip)</option></select></div>
            </div>
            @unless ($filtersValid)<p role="alert" class="mt-3 text-sm text-red-700 dark:text-red-300">Pilih bulan transaksi dan status yang valid.</p>@endunless
        </div>

        <div wire:loading.class="opacity-50" wire:target="search,month,status">
            @if ($items->isNotEmpty())
                <div class="divide-y divide-slate-100 dark:divide-slate-700 md:hidden">
                    @foreach ($items as $payment)
                        <article wire:key="payment-card-{{ $payment->id }}" class="space-y-4 p-5">
                            <div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="break-words font-semibold">{{ $payment->student?->name }}</h3><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $payment->student?->class_name }} · {{ $payment->student?->nis }}</p></div><p class="shrink-0 text-base font-bold tabular-nums">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p></div>
                            <div><p class="text-sm font-medium">{{ $payment->category === 'other' ? 'Lainnya' : 'SPP '.($payment->period?->translatedFormat('F Y') ?? '') }}</p>@if ($payment->category === 'other')<p class="mt-1 whitespace-pre-line break-words text-sm text-slate-600 dark:text-slate-300">{{ $payment->description }}</p>@endif<p class="mt-2 text-xs text-slate-500 dark:text-slate-400">{{ $payment->created_at->translatedFormat('d M Y') }} · {{ $payment->created_at->format('H:i') }}</p>@if ($payment->environment === 'sandbox')<p class="mt-1 text-xs font-semibold text-amber-800 dark:text-amber-300">Simulasi sandbox</p>@endif</div>
                            <div class="flex flex-wrap items-center justify-between gap-2"><x-payment-status :status="$payment->status" /><button type="button" wire:click="reviewPayment({{ $payment->id }})" class="btn-ghost min-h-11" aria-label="Lihat detail pembayaran {{ $payment->student?->name }}">Lihat detail <span aria-hidden="true">→</span></button></div>
                        </article>
                    @endforeach
                </div>
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <caption class="sr-only">Riwayat pembayaran siswa berdasarkan filter pencarian</caption>
                        <thead class="border-b border-slate-100 bg-slate-50/80 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-400"><tr><th scope="col" class="py-3.5 pl-6 pr-4">Siswa</th><th scope="col" class="px-4 py-3.5">Pembayaran</th><th scope="col" class="px-4 py-3.5 text-right">Nominal</th><th scope="col" class="px-4 py-3.5">Status</th><th scope="col" class="px-4 py-3.5">Tanggal</th><th scope="col" class="py-3.5 pl-4 pr-6 text-right"><span class="sr-only">Detail pembayaran</span></th></tr></thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($items as $payment)
                                <tr wire:key="payment-{{ $payment->id }}" class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30">
                                    <td class="py-5 pl-6 pr-4 align-top"><p class="font-semibold">{{ $payment->student?->name }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $payment->student?->class_name }} · {{ $payment->student?->nis }}</p></td>
                                    <td class="max-w-xs px-4 py-5 align-top"><p class="font-medium">{{ $payment->category === 'other' ? 'Lainnya' : 'SPP '.($payment->period?->translatedFormat('F Y') ?? '') }}</p>@if ($payment->category === 'other' && $payment->description)<p class="mt-1 whitespace-pre-line break-words text-xs leading-relaxed text-slate-500 dark:text-slate-400">{{ $payment->description }}</p>@endif @if ($payment->environment === 'sandbox')<p class="mt-1 text-xs font-semibold text-amber-800 dark:text-amber-300">Simulasi sandbox</p>@endif</td>
                                    <td class="whitespace-nowrap px-4 py-5 text-right align-top font-semibold tabular-nums">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-5 align-top"><x-payment-status :status="$payment->status" /></td>
                                    <td class="whitespace-nowrap px-4 py-5 align-top"><p class="text-xs font-medium">{{ $payment->created_at->translatedFormat('d M Y') }}</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $payment->created_at->format('H:i') }}</p></td>
                                    <td class="py-4 pl-4 pr-6 text-right align-top"><button type="button" wire:click="reviewPayment({{ $payment->id }})" class="btn-ghost min-h-11 whitespace-nowrap" aria-label="Lihat detail pembayaran {{ $payment->student?->name }}">Lihat detail <span aria-hidden="true">↗</span></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-14 text-center"><svg class="mx-auto h-9 w-9 text-slate-300 dark:text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4M9 3v6h6M9 3l6 6m-7 5h8m-8 3h5" /></svg><p class="mt-4 text-sm font-semibold">Belum ada riwayat pembayaran yang sesuai.</p><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Coba ubah pencarian, bulan transaksi, atau status pembayaran.</p></div>
            @endif
        </div>
        <div class="space-y-3 border-t border-slate-100 px-5 py-4 dark:border-slate-700 sm:px-6"><p class="text-xs text-slate-500 dark:text-slate-400">Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} pembayaran</p>{{ $items->links() }}</div>
    </section>
    <dialog wire:ignore.self x-data="{ open: $wire.entangle('showReviewModal') }"
        x-effect="if (open) { $nextTick(() => { if (!$el.open) $el.showModal() }) } else if ($el.open) { $el.close() }"
        @cancel.prevent="$wire.closeReviewModal()" aria-labelledby="spp-review-title"
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        <div class="space-y-5 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4"><h2 id="spp-review-title" class="text-lg font-bold">Detail konfirmasi pembayaran</h2><button type="button" wire:click="closeReviewModal" class="btn-ghost">Tutup</button></div>
            @if ($reviewingPayment)
                @php($reviewable = $reviewingPayment->status === 'pending_review' && $reviewingPayment->payment_type === 'qris_static' && $reviewingPayment->environment === 'production')
                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-600 dark:text-slate-300">Siswa / NIS / kelas</dt><dd class="mt-1 font-semibold">{{ $reviewingPayment->student->name }}<br>{{ $reviewingPayment->student->nis }} / {{ $reviewingPayment->student->class_name }}</dd></div>
                    <div><dt class="text-slate-600 dark:text-slate-300">Jenis pembayaran</dt><dd class="mt-1 font-semibold">{{ $reviewingPayment->category === 'other' ? 'Lainnya' : 'SPP '.($reviewingPayment->period?->translatedFormat('F Y') ?? '') }}</dd></div>
                    <div><dt class="text-slate-600 dark:text-slate-300">Pengirim</dt><dd class="mt-1 break-words">{{ $reviewingPayment->payer?->name ?? 'Tanpa akun' }}<br>{{ $reviewingPayment->payer?->email }}</dd></div>
                    <div><dt class="text-slate-600 dark:text-slate-300">Nominal pembayaran</dt><dd class="mt-1 font-semibold tabular-nums">Rp {{ number_format($reviewingPayment->amount, 0, ',', '.') }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-600 dark:text-slate-300">Keterangan orang tua</dt><dd class="mt-1 whitespace-pre-wrap break-words">{{ $reviewingPayment->description ?: '—' }}</dd></div>
                    <div><dt class="text-slate-600 dark:text-slate-300">Dikonfirmasi</dt><dd class="mt-1">{{ $reviewingPayment->confirmed_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-600 dark:text-slate-300">Status konfirmasi</dt><dd class="mt-1 font-semibold"><x-payment-status :status="$reviewingPayment->status" /></dd></div>
                    @if ($reviewingPayment->reviewed_at)<div><dt class="text-slate-600 dark:text-slate-300">Pemeriksa / waktu</dt><dd class="mt-1">{{ $reviewingPayment->reviewer?->name ?? 'Administrator' }}<br>{{ $reviewingPayment->reviewed_at->format('d/m/Y H:i') }}</dd></div>@endif
                    @if ($reviewingPayment->review_notes)<div class="sm:col-span-2"><dt class="text-slate-600 dark:text-slate-300">Catatan / alasan</dt><dd class="mt-1 whitespace-pre-wrap break-words">{{ $reviewingPayment->review_notes }}</dd></div>@endif
                </dl>
                @if ($reviewingPayment->proof_path)
                    <div><a href="{{ route('spp.payment-proof', $reviewingPayment) }}" target="_blank" rel="noopener" class="font-semibold text-brand-700 underline underline-offset-2 dark:text-brand-300">Buka bukti pembayaran privat</a><img src="{{ route('spp.payment-proof', $reviewingPayment) }}" alt="Bukti pembayaran yang diunggah orang tua" class="mt-3 max-h-96 w-full rounded-xl border border-slate-200 object-contain dark:border-slate-700" loading="lazy"></div>
                @else
                    <p class="text-sm text-slate-600 dark:text-slate-300">Bukti pembayaran tidak tersedia pada transaksi ini.</p>
                @endif
                @if ($reviewable)
                    <form wire:submit="approvePayment" class="space-y-4">
                        <p class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-200">Periksa merchant, nominal, periode, dan dana masuk pada rekening sekolah. Persetujuan mencatat pembayaran berhasil; bukti unggahan bukan jaminan dana diterima.</p>
                        @error('payment')<p role="alert" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror
                        <div><label for="spp-review-notes" class="label">Catatan pemeriksaan (wajib untuk penolakan)</label><textarea id="spp-review-notes" wire:model="reviewNotes" maxlength="1000" rows="3" class="input"></textarea>@error('reviewNotes')<p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror</div>
                        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700"><button type="button" wire:click="rejectPayment" wire:loading.attr="disabled" wire:target="approvePayment,rejectPayment" class="btn-danger min-h-11 w-full sm:w-auto"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" /></svg>Tolak konfirmasi</button><button type="submit" wire:loading.attr="disabled" wire:target="approvePayment,rejectPayment" class="btn-primary min-h-11 justify-center w-full sm:w-auto">Setujui pembayaran</button></div>
                    </form>
                @else
                    <p class="text-sm text-slate-600 dark:text-slate-300">Riwayat hanya-baca. Hanya konfirmasi QRIS sekolah yang menunggu verifikasi dapat diproses.</p>
                    @error('payment')<p role="alert" class="text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror
                @endif
            @endif
        </div>
    </dialog>

    <dialog wire:ignore.self x-data="{ open: $wire.entangle('showQrisModal') }"
        x-effect="if (open) { $nextTick(() => { if (!$el.open) $el.showModal() }) } else if ($el.open) { $el.close() }"
        @cancel.prevent="$wire.closeQrisModal()" aria-labelledby="spp-qris-title"
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        <form wire:submit="saveQris" class="space-y-5 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4"><h2 id="spp-qris-title" class="text-lg font-bold">Atur QRIS sekolah</h2><button type="button" wire:click="closeQrisModal" class="btn-ghost">Tutup</button></div>
            <p class="text-sm text-amber-800 dark:text-amber-200">Unggah QRIS asli milik sekolah dari penyedia pembayaran resmi. Verifikasi nama merchant dan uji pindai secara manual. Aplikasi tidak membuat QRIS atau memverifikasi dana otomatis.</p>
            @if ($qrisConfigured)<div><p class="mb-2 text-sm font-semibold">QRIS saat ini</p><a href="{{ route('spp.qris') }}" target="_blank" rel="noopener"><img src="{{ route('spp.qris') }}" alt="QRIS pembayaran sekolah saat ini" class="mx-auto max-h-72 rounded-xl border border-slate-200 object-contain dark:border-slate-700" loading="lazy"></a></div>@endif
            <div><label for="spp-qris-image" class="label">Gambar QRIS</label><input id="spp-qris-image" type="file" wire:model="qrisImage" accept="image/jpeg,image/png,image/webp" class="input" required><p class="mt-1 text-xs text-slate-600 dark:text-slate-300">JPG, JPEG, PNG, atau WebP, maksimal 2 MB. File disimpan privat.</p>@error('qrisImage')<p role="alert" class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror</div>
            <p wire:loading wire:target="qrisImage" class="text-sm">Mengunggah gambar…</p>
            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700"><button type="button" wire:click="closeQrisModal" class="btn-ghost">Batal</button><button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="qrisImage,saveQris">Simpan QRIS</button></div>
        </form>
    </dialog>


</div>
