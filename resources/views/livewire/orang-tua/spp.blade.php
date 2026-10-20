<div class="space-y-6 text-slate-800 dark:text-slate-100">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-brand-600 dark:text-brand-300">Portal orang tua</p>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">SPP anak saya</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Pilih anak dan tagihan, bayar melalui QRIS sekolah, lalu kirim bukti untuk diperiksa admin.</p>
        </div>
        <button type="button" wire:click="openPayment" class="btn-primary min-h-11" @disabled(!$configured || $students->isEmpty())>Bayar SPP</button>
    </div>

    @unless ($configured)
        <div role="status" class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-100">
            QRIS sekolah belum tersedia. Tagihan tetap dapat dilihat; hubungi admin sekolah sebelum melakukan pembayaran.
        </div>
    @endunless

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="card"><p class="text-sm text-slate-600 dark:text-slate-300">Tagihan belum lunas</p><p class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($outstanding, 0, ',', '.') }}</p></div>
        <div class="card"><p class="text-sm text-slate-600 dark:text-slate-300">Total tagihan lunas</p><p class="mt-2 text-2xl font-bold text-brand-600 dark:text-brand-300">Rp {{ number_format($paid, 0, ',', '.') }}</p></div>
    </div>

    <div class="flex flex-wrap gap-2">
        @forelse ($students as $student)
            <span class="rounded-full bg-brand-50 px-3 py-2 text-sm text-brand-800 dark:bg-brand-900 dark:text-brand-100">{{ $student->name }} · {{ $student->class_name }}</span>
        @empty
            <p class="text-sm text-slate-600 dark:text-slate-300">Akun Anda belum terhubung dengan data siswa. Hubungi admin sekolah untuk menghubungkannya.</p>
        @endforelse
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        @forelse ($items as $invoice)
            @php
                $latestPayment = $invoice->payments->first();
                $activePayment = $invoice->payments->first(fn ($payment) => $payment->active_invoice_id !== null || $payment->paid_at !== null || $payment->requires_review || in_array($payment->status, ['pending_review', 'approved'], true));
                $labels = ['unpaid' => 'Belum lunas', 'paid' => 'Lunas', 'waived' => 'Bebas SPP', 'refunded' => 'Dikembalikan / perlu pemeriksaan'];
                $paymentLabels = ['pending_review' => 'Menunggu pemeriksaan admin', 'approved' => 'Disetujui admin', 'rejected' => 'Ditolak admin'];
            @endphp
            <article wire:key="invoice-{{ $invoice->id }}" class="card space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><h3 class="font-bold text-slate-900 dark:text-white">{{ $invoice->student->name }}</h3><p class="text-sm text-slate-600 dark:text-slate-300">SPP {{ $invoice->period->translatedFormat('F Y') }} · {{ $invoice->student->class_name }}</p></div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array($invoice->status, ['paid', 'waived'], true) ? 'bg-brand-100 text-brand-800 dark:bg-brand-900 dark:text-brand-100' : 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-100' }}">{{ $labels[$invoice->status] ?? $invoice->status }}</span>
                </div>
                <div><p class="text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p><p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Jatuh tempo {{ $invoice->due_date->translatedFormat('d F Y') }}@if ($invoice->status === 'unpaid' && $invoice->due_date->isBefore(today())) · Melewati jatuh tempo @endif</p></div>
                @if ($invoice->discount_amount > 0)<p class="text-xs text-slate-600 dark:text-slate-300">Tarif Rp {{ number_format($invoice->base_amount, 0, ',', '.') }} · Potongan/beasiswa Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</p>@endif
                @if ($latestPayment)
                    <div class="space-y-1 rounded-xl bg-slate-50 p-3 text-sm text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        <p class="font-semibold">{{ $paymentLabels[$latestPayment->status] ?? $latestPayment->status }}</p>
                        <p class="break-all text-xs">Referensi {{ $latestPayment->order_id }}</p>
                        @if ($latestPayment->description)<p class="whitespace-pre-line break-words">{{ $latestPayment->description }}</p>@endif
                        @if ($latestPayment->confirmed_at)<p class="text-xs">Dikonfirmasi {{ $latestPayment->confirmed_at->format('d/m/Y H:i') }}</p>@endif
                        @if ($latestPayment->status === 'pending_review')<p>Konfirmasi diterima. Jangan membayar ulang; tagihan lunas setelah persetujuan admin.</p>@endif
                        @if ($latestPayment->status === 'rejected')<p class="font-semibold text-red-700 dark:text-red-300">Alasan penolakan: {{ $latestPayment->review_notes ?: 'Hubungi admin sekolah untuk informasi lebih lanjut.' }}</p>@endif
                        @if ($latestPayment->proof_path)<a href="{{ route('spp.payment-proof', $latestPayment) }}" target="_blank" rel="noopener" class="inline-block py-2 font-semibold text-brand-700 underline underline-offset-2 dark:text-brand-300">Lihat bukti pembayaran</a>@endif
                    </div>
                @endif
                @if ($invoice->status === 'unpaid' && $activePayment && !in_array($activePayment->status, ['pending_review', 'approved'], true))
                    <p role="status" class="rounded-xl bg-amber-50 p-3 text-sm font-semibold text-amber-900 dark:bg-amber-950 dark:text-amber-100">Transaksi sebelumnya masih tercatat. Hubungi admin sekolah untuk pemeriksaan dan jangan membayar ulang.</p>
                @endif
                <div class="flex flex-wrap gap-2">
                    @if ($invoice->status === 'unpaid' && !$activePayment)
                        <button type="button" wire:click="openPayment({{ $invoice->id }})" @disabled(!$configured) class="btn-primary min-h-11 disabled:opacity-50">{{ $latestPayment?->status === 'rejected' ? 'Kirim ulang pembayaran' : 'Bayar SPP' }}</button>
                    @elseif ($invoice->status === 'paid')
                        <a href="{{ route('orang-tua.spp.receipt', $invoice) }}" target="_blank" rel="noopener" class="btn-ghost min-h-11">Lihat / cetak kuitansi</a>
                    @endif
                </div>
                @if ($invoice->payments->count() > 1)
                    <details class="text-sm text-slate-600 dark:text-slate-300"><summary class="cursor-pointer py-2">Riwayat konfirmasi pembayaran</summary><ul class="space-y-3">@foreach ($invoice->payments as $payment)<li class="space-y-1 border-t border-slate-200 pt-3 dark:border-slate-700"><p class="break-all">{{ $payment->created_at->format('d/m/Y H:i') }} · {{ $payment->order_id }} · {{ $paymentLabels[$payment->status] ?? $payment->status }}</p>@if ($payment->description)<p class="whitespace-pre-line break-words">{{ $payment->description }}</p>@endif @if ($payment->review_notes)<p>Catatan admin: {{ $payment->review_notes }}</p>@endif @if ($payment->proof_path)<a href="{{ route('spp.payment-proof', $payment) }}" target="_blank" rel="noopener" class="inline-block py-2 font-semibold text-brand-700 underline dark:text-brand-300">Lihat bukti pembayaran</a>@endif</li>@endforeach</ul></details>
                @endif
            </article>
        @empty
            <div class="card xl:col-span-2"><p class="text-sm text-slate-600 dark:text-slate-300">Belum ada tagihan SPP yang diterbitkan untuk anak Anda.</p></div>
        @endforelse
    </div>
    {{ $items->links() }}
    <p class="text-xs text-slate-600 dark:text-slate-300">Satu konfirmasi membayar satu tagihan bulanan secara penuh. Bukti pembayaran diperiksa manual oleh admin sebelum tagihan dinyatakan lunas.</p>

    <dialog wire:ignore.self x-data="{ open: $wire.entangle('showPaymentModal') }"
        x-effect="if (open) { $nextTick(() => { if (!$el.open) $el.showModal() }) } else if ($el.open) { $el.close() }"
        @cancel.prevent="$wire.closePayment()" aria-labelledby="spp-payment-title" aria-describedby="spp-payment-help"
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        <div class="space-y-5 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4"><h2 id="spp-payment-title" class="text-lg font-bold">Bayar SPP</h2><button type="button" wire:click="closePayment" class="btn-ghost min-h-11" aria-label="Tutup formulir pembayaran">Tutup</button></div>
            <p id="spp-payment-help" class="text-sm text-slate-600 dark:text-slate-300">{{ $paymentStep === 'qris' ? 'Langkah 2 dari 2 — bayar nominal tepat melalui QRIS, lalu konfirmasi pembayaran.' : 'Langkah 1 dari 2 — pilih anak, tagihan, dan isi keterangan pembayaran.' }}</p>
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200"><p class="font-semibold">Pembayaran belum dapat dilanjutkan.</p><ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @if ($showPaymentModal && $paymentStep === 'qris' && $selectedInvoice && $configured)
                <div class="space-y-4">
                    <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-800"><p class="font-semibold">{{ $selectedInvoice->student->name }}</p><p class="text-sm">SPP {{ $selectedInvoice->period->translatedFormat('F Y') }}</p><p class="mt-2 text-2xl font-bold tabular-nums">Rp {{ number_format($selectedInvoice->amount, 0, ',', '.') }}</p><p class="mt-2 whitespace-pre-line break-words text-sm">{{ $description }}</p></div>
                    <p class="text-sm">Pindai QRIS dengan aplikasi bank atau dompet digital. Masukkan nominal tepat <strong>Rp {{ number_format($selectedInvoice->amount, 0, ',', '.') }}</strong> dan periksa nama penerima sebelum membayar.</p>
                    <div class="rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-700"><img src="{{ route('spp.qris') }}" alt="QRIS pembayaran SPP" class="mx-auto h-auto max-h-96 w-full object-contain"></div>
                    <a href="{{ route('spp.qris') }}" download="qris-spp.png" class="btn-ghost flex min-h-11 justify-center">Simpan / unduh QRIS</a>
                    <p class="text-sm text-slate-600 dark:text-slate-300">Anda juga dapat menyimpan gambar QRIS lalu memilihnya dari galeri aplikasi pembayaran. Simpan bukti transaksi yang berhasil.</p>
                    <p class="rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-100">Konfirmasi pembayaran masih memerlukan persetujuan admin. Jangan melakukan pembayaran ulang selama bukti diperiksa.</p>
                    <form wire:submit="confirmPayment" x-data="{ uploading: false }"
                        x-on:livewire-upload-start="uploading = true"
                        x-on:livewire-upload-finish="uploading = false"
                        x-on:livewire-upload-error="uploading = false"
                        x-on:livewire-upload-cancel="uploading = false"
                        class="space-y-4">
                        <div><label for="spp-payment-proof" class="label">Bukti pembayaran</label><input id="spp-payment-proof" type="file" wire:model="proof" accept="image/jpeg,image/png,image/webp" required class="input"><p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Unggah bukti transaksi berhasil: JPG, PNG, atau WebP, maksimal 5 MB.</p>@error('proof')<p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror<p wire:loading wire:target="proof" role="status" class="mt-1 text-sm">Mengunggah bukti pembayaran…</p></div>
                        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700"><button type="button" wire:click="closePayment" class="btn-ghost min-h-11">Tutup</button><button type="submit" class="btn-primary min-h-11 disabled:opacity-50" @disabled(!$proof) x-bind:disabled="uploading || !$wire.proof" wire:loading.attr="disabled" wire:target="proof,confirmPayment"><span wire:loading.remove wire:target="confirmPayment">Saya sudah bayar — kirim konfirmasi</span><span wire:loading wire:target="confirmPayment">Mengirim konfirmasi…</span></button></div>
                    </form>
                </div>
            @else
                <form wire:submit="preparePayment" class="space-y-4">
                    <div><label for="spp-payment-student" class="label">Pilih anak</label><select id="spp-payment-student" wire:model.live="studentId" required class="select" autofocus><option value="">Pilih anak Anda</option>@foreach ($students as $student)<option value="{{ $student->id }}">{{ $student->name }} — {{ $student->class_name }}</option>@endforeach</select>@error('studentId')<p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror</div>
                    <div><label for="spp-payment-invoice" class="label">Tagihan SPP</label><select id="spp-payment-invoice" wire:model="invoiceId" required class="select" @disabled(!$studentId)><option value="">Pilih tagihan yang belum lunas</option>@foreach ($paymentInvoices as $paymentInvoice)<option value="{{ $paymentInvoice->id }}">{{ $paymentInvoice->period->translatedFormat('F Y') }} — Rp {{ number_format($paymentInvoice->amount, 0, ',', '.') }}</option>@endforeach</select>@error('invoiceId')<p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror</div>
                    <div><label for="spp-payment-description" class="label">Keterangan pembayaran</label><textarea id="spp-payment-description" wire:model="description" required maxlength="1000" rows="3" class="input" placeholder="Contoh: SPP Oktober untuk anak saya"></textarea><p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Wajib diisi, maksimal 1.000 karakter. Keterangan ini akan dibaca admin.</p>@error('description')<p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $message }}</p>@enderror</div>
                    <div class="flex justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700"><button type="button" wire:click="closePayment" class="btn-ghost min-h-11">Batal</button><button type="submit" class="btn-primary min-h-11 disabled:opacity-50" @disabled(!$configured) wire:loading.attr="disabled" wire:target="preparePayment">Lanjut ke QRIS</button></div>
                </form>
            @endif
        </div>
    </dialog>
</div>
