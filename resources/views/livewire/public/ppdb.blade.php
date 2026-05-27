@php
    use App\Models\Setting;
    $school = Setting::get('school_name', 'Alifia Modern School');
    $ppdbYear = Setting::get('ppdb_year', '2026/2027');
@endphp
<div>
    {{-- ============================================ --}}
    {{-- HERO PPDB --}}
    {{-- ============================================ --}}
    <section class="relative overflow-hidden py-12 lg:py-16">
        {{-- Decorative blobs --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[5%] left-[5%] w-[420px] h-[420px] bg-brand-200/30 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-[5%] right-[5%] w-[420px] h-[420px] bg-amber-200/25 rounded-full blur-[120px]">
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                {{-- Left Text --}}
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 bg-brand-50 border border-brand-200/60 px-4 py-1.5 rounded-full text-brand-700 text-xs sm:text-sm font-semibold tracking-wide animate-fade-up">
                        <svg class="w-4 h-4 text-brand-600 animate-pulse" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                        </svg>
                        <span>Pendaftaran Online {{ $ppdbYear }}</span>
                    </div>

                    <h1
                        class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.1] animate-fade-up delay-100">
                        Daftar
                        <span class="text-brand-600 relative inline-block">
                            PPDB
                            <span class="absolute bottom-1 left-0 w-full h-3 bg-brand-100/85 -z-10 rounded-full"></span>
                        </span>
                        Sekarang
                    </h1>

                    <p
                        class="text-base sm:text-lg text-slate-600 leading-relaxed font-light max-w-xl mx-auto lg:mx-0 animate-fade-up delay-200">
                        Bergabunglah dengan keluarga besar <span
                            class="font-semibold text-slate-800">{{ $school }}</span>. Lengkapi formulir berikut
                        dan tim kami akan menghubungi Anda untuk proses selanjutnya.
                    </p>

                    {{-- Mini stats --}}
                    <div class="grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0 pt-4 animate-fade-up delay-300">
                        <div class="liquid-glass rounded-2xl p-4 text-center border border-white/80">
                            <div class="text-2xl font-extrabold text-brand-600">
                                {{ Setting::get('stat_students', '1200+') }}</div>
                            <div class="text-[11px] font-semibold text-slate-500 mt-1">Siswa Aktif</div>
                        </div>
                        <div class="liquid-glass rounded-2xl p-4 text-center border border-white/80">
                            <div class="text-2xl font-extrabold text-brand-600">{{ Setting::get('accreditation', 'A') }}
                            </div>
                            <div class="text-[11px] font-semibold text-slate-500 mt-1">Akreditasi</div>
                        </div>
                        <div class="liquid-glass rounded-2xl p-4 text-center border border-white/80">
                            <div class="text-2xl font-extrabold text-brand-600">
                                {{ Setting::get('stat_graduation', '98%') }}</div>
                            <div class="text-[11px] font-semibold text-slate-500 mt-1">Lulusan PTN</div>
                        </div>
                    </div>
                </div>

                {{-- Right Quick Info Card --}}
                <div class="lg:col-span-5 animate-fade-up delay-200">
                    <div class="liquid-glass rounded-[2rem] p-6 sm:p-8 border border-white/80 shadow-lg space-y-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-white flex items-center justify-center shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.26 10.147a60.436 60.436 0 0 0-.491 6.347A48.627 48.627 0 0 1 12 20.904a48.627 48.627 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.57 50.57 0 0 0-2.658-.813A59.905 59.905 0 0 1 12 3.493a59.902 59.902 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-brand-600 uppercase tracking-widest">Tahun Ajaran
                                </div>
                                <div class="text-xl font-bold text-slate-900">{{ $ppdbYear }}</div>
                            </div>
                        </div>

                        <div class="border-t border-white/60"></div>

                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">Pendaftaran Gratis</div>
                                    <div class="text-xs text-slate-500">Tidak ada biaya formulir</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">Proses Cepat</div>
                                    <div class="text-xs text-slate-500">Konfirmasi 1x24 jam kerja</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">Dukungan Penuh</div>
                                    <div class="text-xs text-slate-500">Tim PPDB siap membantu</div>
                                </div>
                            </li>
                        </ul>

                        <div class="border-t border-white/60"></div>

                        <div class="flex items-center gap-3 text-sm">
                            <div
                                class="w-9 h-9 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Butuh
                                    Bantuan?</div>
                                <div class="font-bold text-slate-900">{{ Setting::get('phone', '-') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- ALUR PENDAFTARAN --}}
    {{-- ============================================ --}}
    <section class="py-12 animate-fade-up delay-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <span
                    class="text-xs font-black uppercase tracking-widest text-brand-600 bg-brand-50 px-3.5 py-1.5 rounded-full">
                    Alur Pendaftaran
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mt-3">
                    Empat Langkah Mudah
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @php
                    $steps = [
                        [
                            'title' => 'Isi Formulir',
                            'desc' => 'Lengkapi data calon siswa & orang tua di halaman ini.',
                            'icon' =>
                                'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 19.5h-15',
                        ],
                        [
                            'title' => 'Konfirmasi',
                            'desc' => 'Tim kami akan menghubungi via telepon/email.',
                            'icon' =>
                                'M21.75 9v.906a2.25 2.25 0 01-1.183 1.981l-6.478 3.488M2.25 9v.906a2.25 2.25 0 001.183 1.981l6.478 3.488m8.839 2.51l-4.66-2.51m0 0l-1.023-.55a2.25 2.25 0 00-2.134 0l-1.022.55m0 0l-4.661 2.51m16.5 1.615a2.25 2.25 0 01-1.183 1.98l-6.478 3.488m0 0a2.25 2.25 0 01-2.134 0m2.134 0l-7.5-4.039m0 0l-1.022.55a2.25 2.25 0 01-2.134 0l-1.022-.55m0 0L2.25 13.605',
                        ],
                        [
                            'title' => 'Tes & Wawancara',
                            'desc' => 'Mengikuti tes seleksi sesuai jenjang.',
                            'icon' =>
                                'M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z',
                        ],
                        [
                            'title' => 'Daftar Ulang',
                            'desc' => 'Lengkapi administrasi & jadi bagian ' . $school . '.',
                            'icon' =>
                                'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z',
                        ],
                    ];
                @endphp
                @foreach ($steps as $i => $step)
                    <div
                        class="liquid-glass rounded-[1.75rem] p-6 border border-white/80 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md relative">
                        <div
                            class="absolute -top-3 -left-3 w-9 h-9 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white text-sm font-bold flex items-center justify-center shadow-lg">
                            {{ $i + 1 }}
                        </div>
                        <div
                            class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mb-4 mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $step['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $step['title'] }}</h3>
                        <p class="text-sm text-slate-500 mt-1.5 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- FORM PPDB --}}
    {{-- ============================================ --}}
    <section class="pb-16 animate-fade-up delay-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <span
                    class="text-xs font-black uppercase tracking-widest text-brand-600 bg-brand-50 px-3.5 py-1.5 rounded-full">
                    Formulir Pendaftaran
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 mt-3">
                    Mulai Pendaftaran
                </h2>
                <p class="text-sm text-slate-500 mt-2 max-w-lg mx-auto">
                    Lengkapi semua data yang diperlukan dengan benar. Kami akan memproses dan menghubungi Anda segera.
                </p>
            </div>

            <livewire:public.ppdb-form />
        </div>
    </section>
</div>
