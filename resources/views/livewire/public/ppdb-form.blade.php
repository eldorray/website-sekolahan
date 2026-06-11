<div>
    @if ($registrationNumber)
        {{-- ===== Success State ===== --}}
        <div
            class="liquid-glass rounded-[2rem] p-8 sm:p-12 text-center border border-white/80 shadow-lg max-w-2xl mx-auto">
            <div
                class="mx-auto h-20 w-20 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-500/30 mb-5 animate-fade-up">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="h-10 w-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </div>
            <h3 class="text-3xl font-extrabold text-slate-900 animate-fade-up delay-100">Pendaftaran Berhasil!</h3>
            <p class="text-slate-600 mt-2 animate-fade-up delay-200">Selamat, data Anda telah kami terima. Berikut nomor
                pendaftaran Anda:</p>

            <div
                class="mt-6 inline-flex items-center gap-3 rounded-2xl bg-brand-50 border-2 border-dashed border-brand-300 px-6 py-4 animate-fade-up delay-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6 text-brand-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
                <div class="text-2xl sm:text-3xl font-extrabold text-brand-700 tracking-wider">{{ $registrationNumber }}
                </div>
            </div>

            <p class="text-xs text-slate-500 mt-4 max-w-md mx-auto animate-fade-up delay-300">
                Simpan nomor pendaftaran ini sebagai bukti. Tim kami akan menghubungi Anda melalui kontak yang
                didaftarkan untuk proses selanjutnya.
            </p>

            <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center animate-fade-up delay-300">
                <a href="{{ route('home') }}" wire:navigate
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    Ke Beranda
                </a>
                <a href="{{ route('contact') }}" wire:navigate
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold transition shadow-md shadow-brand-500/30">
                    Hubungi Tim PPDB
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    @else
        {{-- ===== Form ===== --}}
        @if ($errors->any())
            <div
                class="mb-5 rounded-2xl border border-red-200 bg-red-50/80 backdrop-blur-sm p-4 flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 text-red-500 shrink-0 mt-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                </svg>
                <div class="text-sm">
                    <div class="font-semibold text-red-800">Mohon perbaiki data berikut:</div>
                    <ul class="mt-1 list-disc list-inside text-red-600 text-xs space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form wire:submit="submit"
            class="liquid-glass rounded-[2rem] p-6 sm:p-8 lg:p-10 border border-white/80 shadow-lg space-y-8">

            <x-honeypot livewire-model="extraFields" />
            @error('throttle')
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-sm">{{ $message }}</div>
            @enderror

            {{-- ===== Section: Calon Siswa ===== --}}
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="w-11 h-11 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Data Calon Siswa</h3>
                        <p class="text-xs text-slate-500">Informasi pribadi calon peserta didik.</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input wire:model="full_name"
                            class="input @error('full_name') !border-red-300 !bg-red-50/40 @enderror"
                            placeholder="Sesuai akta kelahiran">
                        @error('full_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label">Nama Panggilan</label>
                        <input wire:model="nickname" class="input" placeholder="Mis. Naufal">
                    </div>
                    <div>
                        <label class="label">Jenis Kelamin <span class="text-red-500">*</span></label>
                        <select wire:model="gender" class="select">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Tempat Lahir <span class="text-red-500">*</span></label>
                        <input wire:model="birthplace"
                            class="input @error('birthplace') !border-red-300 !bg-red-50/40 @enderror"
                            placeholder="Mis. Jakarta">
                        @error('birthplace')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label">Tanggal Lahir <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="birthdate"
                            class="input @error('birthdate') !border-red-300 !bg-red-50/40 @enderror">
                        @error('birthdate')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Jenjang yang Dituju <span class="text-red-500">*</span></label>
                        <select wire:model="grade_target" class="select">
                            <optgroup label="Taman Kanak-kanak">
                                <option>TK A</option>
                                <option>TK B</option>
                            </optgroup>
                            <optgroup label="Sekolah Dasar">
                                <option>SD Kelas 1</option>
                                <option>SD Kelas 2</option>
                                <option>SD Kelas 3</option>
                            </optgroup>
                            <optgroup label="Sekolah Menengah">
                                <option>SMP Kelas 7</option>
                                <option>SMP Kelas 8</option>
                                <option>SMA Kelas 10</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Asal Sekolah</label>
                        <input wire:model="previous_school" class="input"
                            placeholder="Kosongkan jika belum pernah sekolah">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Alamat Rumah <span class="text-red-500">*</span></label>
                        <textarea wire:model="address" rows="3"
                            class="textarea @error('address') !border-red-300 !bg-red-50/40 @enderror"
                            placeholder="Alamat lengkap beserta kode pos"></textarea>
                        @error('address')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="border-t border-white/60"></div>

            {{-- ===== Section: Orang Tua ===== --}}
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Data Orang Tua / Wali</h3>
                        <p class="text-xs text-slate-500">Kontak yang akan kami hubungi untuk konfirmasi.</p>
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Nama Ayah <span class="text-red-500">*</span></label>
                        <input wire:model="father_name"
                            class="input @error('father_name') !border-red-300 !bg-red-50/40 @enderror">
                        @error('father_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label">Nama Ibu <span class="text-red-500">*</span></label>
                        <input wire:model="mother_name"
                            class="input @error('mother_name') !border-red-300 !bg-red-50/40 @enderror">
                        @error('mother_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                        <input wire:model="parent_phone"
                            class="input @error('parent_phone') !border-red-300 !bg-red-50/40 @enderror"
                            placeholder="Contoh: 0812xxxxxxxx">
                        @error('parent_phone')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" wire:model="parent_email"
                            class="input @error('parent_email') !border-red-300 !bg-red-50/40 @enderror"
                            placeholder="nama@email.com">
                        @error('parent_email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="border-t border-white/60"></div>

            {{-- ===== Dokumen ===== --}}
            <section>
                <div class="flex items-center gap-3 mb-5">
                    <h3 class="font-bold text-slate-900">Dokumen Pendukung</h3>
                </div>
                <p class="text-xs text-slate-500 mb-4">Unggah berkas dalam format JPG, PNG, atau PDF (maks. 2 MB per
                    berkas).</p>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="label">Kartu Keluarga (KK) <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="kk_file" accept=".jpg,.jpeg,.png,.pdf"
                            class="input @error('kk_file') !border-red-300 !bg-red-50/40 @enderror">
                        <div wire:loading wire:target="kk_file" class="text-xs text-slate-400 mt-1">Mengunggah…</div>
                        @error('kk_file')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="label">Akte Kelahiran <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="birth_certificate_file" accept=".jpg,.jpeg,.png,.pdf"
                            class="input @error('birth_certificate_file') !border-red-300 !bg-red-50/40 @enderror">
                        <div wire:loading wire:target="birth_certificate_file" class="text-xs text-slate-400 mt-1">
                            Mengunggah…</div>
                        @error('birth_certificate_file')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <div class="border-t border-white/60"></div>

            {{-- ===== Submit ===== --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <p class="text-xs text-slate-500 max-w-md">
                    Dengan mengirimkan formulir ini, Anda menyetujui data tersebut diproses untuk keperluan PPDB.
                </p>
                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-full bg-gradient-to-r from-brand-500 to-brand-700 text-white text-sm font-semibold shadow-lg shadow-brand-500/30 hover:shadow-xl hover:shadow-brand-500/40 hover:-translate-y-0.5 transition disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                    <span wire:loading.remove wire:target="submit" class="inline-flex items-center gap-2">
                        Kirim Pendaftaran
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z">
                            </path>
                        </svg>
                        Mengirim...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
