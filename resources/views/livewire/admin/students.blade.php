<div class="space-y-6 text-slate-800 dark:text-slate-100">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">Siswa</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Sinkronkan siswa MI dan SMP dari Data Induk, lalu atur tarif SPP dan akun orang tua.</p>
        </div>
        <button type="button" wire:click="openSync" class="btn-primary min-h-11">Sinkron dari Data Induk</button>
    </div>

    @if ($syncSummary)
        <div role="status" class="space-y-2 rounded-2xl bg-brand-50 p-4 text-sm text-brand-900 dark:bg-brand-950 dark:text-brand-100">
            <p class="font-semibold">{{ $syncSummary }}</p>
            <p>Data tanpa NISN, data tidak valid, atau identitas yang bentrok dilewati. Periksa data sumber jika ada siswa yang belum masuk.</p>
        </div>
    @endif

    <div class="card dark:border-slate-700 dark:bg-slate-900">
        <p class="mb-4 text-sm text-slate-600 dark:text-slate-300">Pembayaran SPP/Lainnya dapat dilakukan tanpa login menggunakan NISN atau nama lengkap siswa. Akun orang tua di <a href="{{ route('admin.users') }}" wire:navigate class="font-semibold text-brand-700 underline dark:text-brand-300">Pengguna</a> bersifat opsional untuk mengakses bukti dan kuitansi arsip.</p>
        <div class="mb-4 max-w-sm"><label for="student-search" class="label">Cari siswa</label><input id="student-search" type="search" wire:model.live.debounce.300ms="search" placeholder="Nama, NIS/NISN, atau kelas" class="input"></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[780px] text-left text-sm">
                <caption class="sr-only">Daftar siswa dan tarif SPP bulanan</caption>
                <thead class="border-b border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-300">
                    <tr><th scope="col" class="px-3 py-3">Siswa / NIS/NISN</th><th scope="col" class="px-3 py-3">Kelas</th><th scope="col" class="px-3 py-3">Tarif / potongan</th><th scope="col" class="px-3 py-3">Orang tua</th><th scope="col" class="px-3 py-3">Status</th><th scope="col" class="px-3 py-3 text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($items as $student)
                        <tr wire:key="student-{{ $student->id }}">
                            <td class="px-3 py-4"><div class="font-semibold">{{ $student->name }}</div><div class="mt-1 text-xs text-slate-600 dark:text-slate-400">{{ $student->nis }}@if ($student->data_induk_source) · {{ strtoupper($student->data_induk_source) }} @endif</div></td>
                            <td class="px-3 py-4">{{ $student->class_name }}</td>
                            <td class="px-3 py-4 tabular-nums"><div>Rp {{ number_format($student->monthly_fee, 0, ',', '.') }}</div><div class="mt-1 text-xs text-slate-600 dark:text-slate-400">Potongan Rp {{ number_format($student->discount_amount, 0, ',', '.') }}</div></td>
                            <td class="px-3 py-4">{{ $student->guardians->pluck('name')->join(', ') ?: 'Belum terhubung' }}</td>
                            <td class="px-3 py-4"><span class="rounded-full px-2 py-1 text-xs {{ $student->is_active ? 'bg-brand-100 text-brand-800 dark:bg-brand-900 dark:text-brand-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">{{ $student->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="px-3 py-4 text-right"><button type="button" wire:click="edit({{ $student->id }})" class="btn-ghost" aria-label="Edit siswa {{ $student->name }}">Edit</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-10 text-center text-slate-600 dark:text-slate-300">Belum ada siswa. Sinkronkan dari Data Induk untuk menerima pembayaran sekolah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $items->links() }}</div>
    </div>

    <dialog wire:ignore.self x-data="{ open: $wire.entangle('showSyncModal') }"
        x-effect="if (open) { $nextTick(() => { if (!$el.open) $el.showModal() }) } else if ($el.open) { $el.close() }"
        @cancel.prevent="$wire.closeSync()" aria-labelledby="student-sync-title" aria-describedby="student-sync-help"
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        <form wire:submit="syncStudents" class="space-y-5 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <h2 id="student-sync-title" class="text-lg font-bold">Sinkron dari Data Induk</h2>
                <button type="button" wire:click="closeSync" class="btn-ghost min-h-11" wire:loading.attr="disabled" wire:target="syncStudents">Tutup</button>
            </div>
            <p id="student-sync-help" class="text-sm text-slate-600 dark:text-slate-300">Ambil identitas, kelas, dan status siswa dari sumber MI atau SMP. Tarif, potongan, tautan wali, serta tagihan siswa yang sudah ada tetap dipertahankan.</p>
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <div>
                <label for="student-sync-url" class="label">URL Data Induk</label>
                <input id="student-sync-url" wire:model="syncUrl" type="url" maxlength="2000" required class="input min-h-11" placeholder="https://data.sekolah.sch.id" aria-describedby="student-sync-url-help">
                <p id="student-sync-url-help" class="mt-1 text-xs text-slate-600 dark:text-slate-300">URL utama aplikasi Data Induk, tanpa /api. Disimpan setelah berhasil dan terikat ke siswa yang sudah disinkronkan.</p>
            </div>
            <div>
                <label for="student-sync-source" class="label">Sumber siswa</label>
                <select id="student-sync-source" wire:model="syncSource" class="select min-h-11" required><option value="mi">MI</option><option value="smp">SMP</option></select>
                <p class="mt-1 text-xs text-slate-600 dark:text-slate-300">Sinkronkan bergantian untuk mengambil kedua unit. Siswa yang sudah nonaktif di unit lama dapat berpindah ke unit baru yang aktif jika NISN dan namanya cocok. Bentrok siswa aktif dilewati.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label for="student-sync-fee" class="label">Tarif siswa baru (Rp/bulan)</label><input id="student-sync-fee" wire:model="syncMonthlyFee" type="number" min="0" max="100000000" step="1" required class="input min-h-11"></div>
                <div><label for="student-sync-start" class="label">Mulai penagihan siswa baru</label><input id="student-sync-start" wire:model="syncBillingStart" type="date" required class="input min-h-11"></div>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-300">NISN dari Data Induk digunakan untuk pencarian pembayaran. Tarif dan tanggal di atas hanya berlaku untuk siswa baru; tarif 0 berarti bebas SPP. Sinkronisasi tidak menerbitkan tagihan atau menghapus siswa lokal.</p>
            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                <button type="button" wire:click="closeSync" class="btn-ghost min-h-11" wire:loading.attr="disabled" wire:target="syncStudents">Batal</button>
                <button type="submit" class="btn-primary min-h-11 disabled:opacity-50" wire:loading.attr="disabled" wire:target="syncStudents"><span wire:loading.remove wire:target="syncStudents">Sinkron siswa</span><span wire:loading wire:target="syncStudents" role="status">Menyinkronkan…</span></button>
            </div>
        </form>
    </dialog>

    <dialog wire:ignore.self x-data="{ open: $wire.entangle('showModal') }"
        x-effect="if (open) { $nextTick(() => { if (!$el.open) $el.showModal() }) } else if ($el.open) { $el.close() }"
        @cancel.prevent="$wire.closeModal()" aria-labelledby="student-modal-title" aria-describedby="student-modal-description"
        class="m-auto max-h-[90dvh] w-[calc(100%-2rem)] max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
        <form wire:submit="save" class="space-y-5 p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <h2 id="student-modal-title" class="text-lg font-bold">{{ $editingId ? 'Edit siswa' : 'Tambah siswa' }}</h2>
                <button type="button" wire:click="closeModal" class="btn-ghost" aria-label="Tutup formulir siswa">Tutup</button>
            </div>
            <p id="student-modal-description" class="text-sm text-slate-600 dark:text-slate-300">Perubahan tarif berlaku untuk pembayaran SPP berikutnya. Riwayat pembayaran dan nominal arsip tetap dipertahankan.</p>
            @if ($errors->any())
                <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
                    <p class="font-semibold">Data belum dapat disimpan. Periksa isian berikut.</p>
                    <ul class="mt-1 list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <div><label for="student-nis" class="label">NIS / NISN</label><input id="student-nis" wire:model="nis" type="text" maxlength="50" required class="input" autofocus></div>
                <div><label for="student-name" class="label">Nama lengkap</label><input id="student-name" wire:model="name" type="text" maxlength="160" required class="input"></div>
                <div><label for="student-class" class="label">Kelas</label><input id="student-class" wire:model="class_name" type="text" maxlength="80" required class="input" placeholder="Contoh: VII A"></div>
                <div><label for="student-start" class="label">Mulai penagihan</label><input id="student-start" wire:model="billing_start" type="date" required class="input"></div>
                <div><label for="student-fee" class="label">Tarif bulanan (Rp)</label><input id="student-fee" wire:model="monthly_fee" type="number" min="0" max="100000000" step="1" required class="input"></div>
                <div><label for="student-discount" class="label">Potongan / beasiswa (Rp)</label><input id="student-discount" wire:model="discount_amount" type="number" min="0" max="100000000" step="1" required class="input"></div>
            </div>
            <p class="text-xs text-slate-600 dark:text-slate-400">Isi rupiah bulat tanpa titik/desimal. Tarif nol atau potongan penuh menghasilkan pembebasan SPP.</p>
            <fieldset>
                <legend class="label">Akun orang tua</legend>
                <p class="mb-2 text-xs text-slate-600 dark:text-slate-400">Hanya akun orang tua aktif yang dapat dihubungkan.</p>
                @if ($hasUnavailableGuardians)<p class="mb-2 text-sm text-amber-800 dark:text-amber-200">Tautan akun yang nonaktif atau bukan orang tua akan dilepas saat disimpan.</p>@endif
                <div class="max-h-44 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-3 dark:border-slate-700">
                    @forelse ($guardians as $guardian)
                        <label wire:key="guardian-{{ $guardian->id }}" class="flex items-start gap-3 text-sm"><input type="checkbox" wire:model="guardianIds" value="{{ $guardian->id }}" class="mt-1"><span>{{ $guardian->name }} <span class="block break-all text-xs text-slate-600 dark:text-slate-400">{{ $guardian->email }}</span></span></label>
                    @empty
                        <p class="text-sm text-slate-600 dark:text-slate-300">Belum ada akun orang tua aktif. Buat akun di Pengguna terlebih dahulu; siswa tetap dapat disimpan tanpa tautan.</p>
                    @endforelse
                </div>
            </fieldset>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active">Siswa aktif</label>
            <p class="text-xs text-slate-600 dark:text-slate-400">Siswa nonaktif tidak dapat dipilih pada pembayaran publik. Riwayat tetap tersimpan.</p>
            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                <button type="button" wire:click="closeModal" class="btn-ghost">Batal</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="save"><span wire:loading.remove wire:target="save">Simpan siswa</span><span wire:loading wire:target="save">Menyimpan…</span></button>
            </div>
        </form>
    </dialog>
</div>
