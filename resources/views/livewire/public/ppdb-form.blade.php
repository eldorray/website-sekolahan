<div>
    @if ($registrationNumber)
        <div class="card text-center">
            <div class="mx-auto h-14 w-14 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            </div>
            <h3 class="text-2xl font-bold text-slate-900">Pendaftaran Berhasil!</h3>
            <p class="text-slate-600 mt-2">Nomor pendaftaran Anda:</p>
            <div class="mt-2 inline-block rounded-xl bg-brand-50 px-5 py-2 text-lg font-bold text-brand-700">{{ $registrationNumber }}</div>
            <p class="text-xs text-slate-500 mt-4">Simpan nomor pendaftaran ini. Tim kami akan menghubungi Anda untuk proses selanjutnya.</p>
        </div>
    @else
    <form wire:submit="submit" class="card space-y-5">
        <div>
            <h3 class="font-bold text-slate-900 mb-3">Data Calon Siswa</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="label">Nama Lengkap</label><input wire:model="full_name" class="input" required>@error('full_name')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                <div><label class="label">Nama Panggilan</label><input wire:model="nickname" class="input"></div>
                <div><label class="label">Jenis Kelamin</label>
                    <select wire:model="gender" class="select"><option value="L">Laki-laki</option><option value="P">Perempuan</option></select>
                </div>
                <div><label class="label">Jenjang yang Dituju</label>
                    <select wire:model="grade_target" class="select">
                        <option>TK A</option><option>TK B</option>
                        <option>SD Kelas 1</option><option>SD Kelas 2</option><option>SD Kelas 3</option>
                        <option>SMP Kelas 7</option><option>SMP Kelas 8</option>
                        <option>SMA Kelas 10</option>
                    </select>
                </div>
                <div><label class="label">Tempat Lahir</label><input wire:model="birthplace" class="input" required></div>
                <div><label class="label">Tanggal Lahir</label><input type="date" wire:model="birthdate" class="input" required>@error('birthdate')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-2"><label class="label">Asal Sekolah (jika ada)</label><input wire:model="previous_school" class="input"></div>
                <div class="sm:col-span-2"><label class="label">Alamat</label><textarea wire:model="address" rows="2" class="textarea" required></textarea></div>
            </div>
        </div>

        <div>
            <h3 class="font-bold text-slate-900 mb-3">Data Orang Tua / Wali</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="label">Nama Ayah</label><input wire:model="father_name" class="input" required></div>
                <div><label class="label">Nama Ibu</label><input wire:model="mother_name" class="input" required></div>
                <div><label class="label">No. Telepon</label><input wire:model="parent_phone" class="input" required></div>
                <div><label class="label">Email</label><input type="email" wire:model="parent_email" class="input"></div>
            </div>
        </div>

        <button class="btn-primary w-full justify-center">Kirim Pendaftaran</button>
    </form>
    @endif
</div>
