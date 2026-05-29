@php use App\Models\Setting; @endphp
<div>
    @push('styles')
        <style>
            @keyframes float-slower {

                0%,
                100% {
                    transform: translate3d(0, 0, 0) scale(1);
                }

                50% {
                    transform: translate3d(0, -20px, 0) scale(1.05);
                }
            }

            @keyframes float-faster {

                0%,
                100% {
                    transform: translate3d(0, 0, 0) scale(1);
                }

                50% {
                    transform: translate3d(0, 18px, 0) scale(0.95);
                }
            }

            .animate-float-1 {
                animation: float-slower 9s ease-in-out infinite;
            }

            .animate-float-2 {
                animation: float-faster 7s ease-in-out infinite;
            }

            .google-shadow-lg {
                box-shadow: 0 4px 4px 0 rgba(60, 64, 67, 0.30), 0 8px 12px 6px rgba(60, 64, 67, 0.15);
            }

            .google-shadow-md {
                box-shadow: 0 1px 3px 0 rgba(60, 64, 67, 0.3), 0 4px 8px 3px rgba(60, 64, 67, 0.15);
            }

            .active-tab-glow {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
            }

            /* ===== Animate On Scroll: zoom-out-up ===== */
            [data-aos="zoom-out-up"] {
                opacity: 0;
                transform: translate3d(0, 60px, 0) scale(1.15);
                transition:
                    opacity 800ms cubic-bezier(0.16, 1, 0.3, 1),
                    transform 800ms cubic-bezier(0.16, 1, 0.3, 1);
                will-change: opacity, transform;
            }

            [data-aos="zoom-out-up"].aos-show {
                opacity: 1;
                transform: translate3d(0, 0, 0) scale(1);
            }

            /* Honor reduced motion */
            @media (prefers-reduced-motion: reduce) {
                [data-aos="zoom-out-up"] {
                    opacity: 1 !important;
                    transform: none !important;
                    transition: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                function bootAOS() {
                    const items = document.querySelectorAll('[data-aos="zoom-out-up"]:not(.aos-bound)');
                    if (!items.length) return;

                    if (!('IntersectionObserver' in window)) {
                        items.forEach(el => el.classList.add('aos-show', 'aos-bound'));
                        return;
                    }

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const el = entry.target;
                                const delay = parseInt(el.dataset.aosDelay || '0', 10);
                                setTimeout(() => el.classList.add('aos-show'), delay);
                                observer.unobserve(el);
                            }
                        });
                    }, {
                        threshold: 0.12,
                        rootMargin: '0px 0px -60px 0px'
                    });

                    items.forEach(el => {
                        el.classList.add('aos-bound');
                        observer.observe(el);
                    });
                }

                document.addEventListener('DOMContentLoaded', bootAOS);
                document.addEventListener('livewire:navigated', bootAOS);
            })();
        </script>
    @endpush

    {{-- ============================================ --}}
    {{-- SECTION 1: HERO --}}
    {{-- ============================================ --}}
    <section id="home" class="relative min-h-[calc(100vh-200px)] flex items-center py-12 overflow-hidden">
        {{-- Parallax Background Blobs --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[8%] left-[2%] w-[450px] h-[450px] bg-brand-200/25 rounded-full blur-[120px]"></div>
            <div class="absolute top-[28%] right-[5%] w-[400px] h-[400px] bg-amber-200/20 rounded-full blur-[110px]">
            </div>
            <div
                class="absolute top-[12%] right-[15%] w-24 h-24 border-8 border-brand-400/20 rounded-full hidden lg:block">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                {{-- Left: Text & CTA --}}
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    {{-- Badge --}}
                    <div data-aos="zoom-out-up"
                        class="inline-flex items-center space-x-2 bg-brand-50 border border-brand-200/60 px-4 py-1.5 rounded-full text-brand-700 text-xs sm:text-sm font-semibold tracking-wide">
                        <svg class="w-4 h-4 text-brand-600 animate-pulse" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                        </svg>
                        <span>{{ Setting::get('ppdb_badge', 'Penerimaan Siswa Baru TA ' . Setting::get('ppdb_year', '2026/2027') . ' Telah Dibuka') }}</span>
                    </div>

                    {{-- Heading --}}
                    <h1 data-aos="zoom-out-up" data-aos-delay="100"
                        class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.1] sm:leading-tight">
                        {{ Setting::get('hero_title_1', 'Membentuk') }}
                        <span class="text-brand-600 relative inline-block">
                            {{ Setting::get('hero_title_2', 'Masa Depan') }}
                            <span class="absolute bottom-2 left-0 w-full h-3 bg-brand-100/85 -z-10 rounded-full"></span>
                        </span>
                        {{ Setting::get('hero_title_3', 'yang Inovatif & Berkarakter') }}
                    </h1>

                    {{-- Subtitle --}}
                    <p data-aos="zoom-out-up" data-aos-delay="200"
                        class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light">
                        {!! Setting::get(
                            'hero_subtitle',
                            'Selamat datang di ' .
                                $school .
                                '. Kami menghadirkan pendidikan akademis bertaraf internasional dengan ekosistem digital cerdas, kurikulum adaptif, dan pembinaan karakter berbasis nilai luhur.',
                        ) !!}
                    </p>

                    {{-- CTA Buttons --}}
                    <div data-aos="zoom-out-up" data-aos-delay="300"
                        class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('ppdb.create') }}" wire:navigate
                            class="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-4 rounded-full text-base apple-transition hover:shadow-xl hover:shadow-brand-500/20 transform hover:-translate-y-0.5 text-center">
                            Mulai Pendaftaran
                        </a>
                        <a href="#about"
                            class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-700 font-semibold px-8 py-4 rounded-full text-base border border-slate-200/85 shadow-sm apple-transition flex items-center justify-center space-x-2 transform hover:-translate-y-0.5">
                            <span>Eksplorasi Profil</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>

                    {{-- Stats Badges --}}
                    <div data-aos="zoom-out-up" data-aos-delay="400"
                        class="pt-8 border-t border-slate-200/60 grid grid-cols-3 gap-4 max-w-md mx-auto lg:mx-0">
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-4.5A3.375 3.375 0 0012.75 10.5h-1.5A3.375 3.375 0 007.5 14.25v4.5m9 0H7.5" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold">Akreditasi</h4>
                                <p class="text-xs text-slate-500">{{ Setting::get('accreditation', 'Unggul (A)') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold">{{ Setting::get('stat_students', '1200+') }}</h4>
                                <p class="text-xs text-slate-500">Siswa Aktif</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold">{{ Setting::get('stat_programs', '15+') }}</h4>
                                <p class="text-xs text-slate-500">Program Unggulan</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Hero Image Card --}}
                <div class="lg:col-span-5 relative">
                    <div data-aos="zoom-out-up" data-aos-delay="200" class="relative mx-auto max-w-md lg:max-w-none">
                        {{-- Main Card --}}
                        <div
                            class="bg-white rounded-[32px] p-6 google-shadow-lg border border-slate-100 transform rotate-1 hover:rotate-0 transition-all duration-500 relative z-20">
                            <div class="relative rounded-2xl overflow-hidden aspect-square shadow-inner">
                                <img src="{{ Setting::imageUrl('hero_image') ?? 'https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&q=80&w=600' }}"
                                    alt="{{ $school }}" fetchpriority="high" decoding="async"
                                    class="w-full h-full object-cover">
                                {{-- Glassmorphism Badge --}}
                                <div
                                    class="absolute bottom-4 left-4 right-4 bg-white/85 backdrop-blur-md rounded-xl p-4 flex items-center justify-between border border-white/20">
                                    <div>
                                        <p class="text-xs font-semibold text-brand-600 uppercase tracking-widest">
                                            {{ Setting::get('hero_badge_label', 'Kolaborasi Aktif') }}</p>
                                        <h4 class="text-sm font-bold text-slate-900 mt-0.5">
                                            {{ Setting::get('hero_badge_text', 'Siswa Mengembangkan Proyek Inovatif') }}
                                        </h4>
                                    </div>
                                    <a href="{{ route('programs.index') }}" wire:navigate
                                        class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white hover:bg-brand-700 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            {{-- Stats Panel --}}
                            <div class="mt-5 grid grid-cols-2 gap-4">
                                <div class="bg-brand-50/60 p-4 rounded-2xl border border-brand-100">
                                    <span
                                        class="text-2xl font-black text-brand-600">{{ Setting::get('stat_graduation', '98%') }}</span>
                                    <p class="text-xs font-semibold text-slate-700 mt-1">
                                        {{ Setting::get('stat_graduation_label', 'Lulusan Melanjutkan ke SMAN/SMKN') }}
                                    </p>
                                </div>
                                <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-100">
                                    <span
                                        class="text-2xl font-black text-emerald-600">{{ Setting::get('stat_facility', '100%') }}</span>
                                    <p class="text-xs font-semibold text-slate-700 mt-1">
                                        {{ Setting::get('stat_facility_label', 'Fasilitas Modern & Lengkap') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Floating Decorations --}}
                        <div
                            class="absolute top-10 -right-6 w-32 h-32 bg-amber-100/80 border border-amber-200 rounded-[24px] -z-10 animate-float-1 flex flex-col justify-center items-center shadow-lg shadow-amber-500/5">
                            <svg class="w-8 h-8 text-amber-500 mb-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <span class="text-xs font-bold text-amber-800">Kreatif</span>
                        </div>

                        <div
                            class="absolute -bottom-8 -left-8 w-40 h-40 bg-red-50/90 border border-red-100 rounded-[28px] -z-10 animate-float-2 flex flex-col justify-center items-center p-4 shadow-lg shadow-red-500/5">
                            <div class="flex -space-x-2 mb-2">
                                <div
                                    class="w-8 h-8 rounded-full bg-brand-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">
                                    1</div>
                                <div
                                    class="w-8 h-8 rounded-full bg-amber-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">
                                    2</div>
                                <div
                                    class="w-8 h-8 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">
                                    3</div>
                            </div>
                            <span class="text-xs font-bold text-red-800">Program Juara</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 2: ABOUT US --}}
    {{-- ============================================ --}}
    <section id="about"
        class="py-24 bg-white/40 border-y border-slate-100 relative overflow-hidden -mx-6 sm:-mx-8 lg:-mx-10 px-6 sm:px-8 lg:px-10">
        {{-- Background Blobs --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[10%] -left-[5%] w-[500px] h-[500px] bg-emerald-100/30 rounded-full blur-[130px]">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            {{-- Section Header --}}
            <div data-aos="zoom-out-up" class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span
                    class="text-xs font-black uppercase tracking-widest text-brand-600 bg-brand-50 px-3.5 py-1.5 rounded-full">
                    Pilar Pendidikan Kami
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                    Membangun Fondasi Masa Depan yang Kokoh
                </h2>
                <p class="text-base text-slate-500">
                    {{ Setting::get('about_subtitle', $school . ' didirikan untuk menjadi pionir pendidikan terintegrasi teknologi, tanpa melupakan penanaman moral yang luhur.') }}
                </p>
            </div>

            {{-- Three Pillars --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
                @foreach ($programs->take(3) as $index => $program)
                    @php
                        $colors = [
                            [
                                'bg' => 'bg-brand-50',
                                'text' => 'text-brand-600',
                                'hover_bg' => 'group-hover:bg-brand-600',
                                'border' => 'hover:border-brand-100',
                                'link' => 'text-brand-600',
                            ],
                            [
                                'bg' => 'bg-emerald-50',
                                'text' => 'text-emerald-600',
                                'hover_bg' => 'group-hover:bg-emerald-600',
                                'border' => 'hover:border-emerald-100',
                                'link' => 'text-emerald-600',
                            ],
                            [
                                'bg' => 'bg-amber-50',
                                'text' => 'text-amber-500',
                                'hover_bg' => 'group-hover:bg-amber-400',
                                'border' => 'hover:border-amber-100',
                                'link' => 'text-amber-600',
                            ],
                        ];
                        $c = $colors[$index % 3];
                    @endphp
                    <div data-aos="zoom-out-up" data-aos-delay="{{ $index * 150 }}"
                        class="bg-white/80 backdrop-blur-sm h-full rounded-[28px] p-8 border border-slate-100 {{ $c['border'] }} apple-transition hover:shadow-xl hover:-translate-y-2.5 group">
                        <div
                            class="w-14 h-14 rounded-2xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center mb-6 {{ $c['hover_bg'] }} group-hover:text-white transition-colors duration-300">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $program->title }}</h3>
                        <p class="text-sm text-slate-600 leading-relaxed font-light mb-4">
                            {{ $program->short_description }}</p>
                        <a href="{{ route('programs.show', $program->slug) }}" wire:navigate
                            class="text-xs font-bold {{ $c['link'] }} flex items-center space-x-1 mt-2 group-hover:translate-x-1 transition-transform">
                            <span>Lihat Detail</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                @endforeach
            </div>

            {{-- Visi Misi Sejarah Tabs --}}
            <div data-aos="zoom-out-up" x-data="{ activeTab: 'visi' }"
                class="bg-white/95 backdrop-blur-md rounded-[32px] p-6 sm:p-10 border border-slate-200/60 shadow-sm">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                    {{-- Tab Selector --}}
                    <div class="lg:col-span-4 flex flex-col space-y-3">
                        <h3 class="text-2xl font-black text-slate-900 mb-2">Profil Inti Sekolah</h3>

                        <button @click="activeTab = 'visi'"
                            :class="activeTab === 'visi' ? 'bg-brand-600 border-brand-600 text-white active-tab-glow' :
                                'bg-white border-slate-200/60 hover:border-slate-300 text-slate-700'"
                            class="text-left p-4 rounded-2xl border transition-all duration-300 flex items-start space-x-4">
                            <div :class="activeTab === 'visi' ? 'bg-white/15 text-white' : 'bg-brand-50 text-brand-600'"
                                class="p-2 rounded-xl">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm sm:text-base">Visi Sekolah</h4>
                                <p :class="activeTab === 'visi' ? 'text-white/85' : 'text-slate-400'"
                                    class="text-xs mt-0.5">Tujuan akhir & impian bersama</p>
                            </div>
                        </button>

                        <button @click="activeTab = 'misi'"
                            :class="activeTab === 'misi' ? 'bg-brand-600 border-brand-600 text-white active-tab-glow' :
                                'bg-white border-slate-200/60 hover:border-slate-300 text-slate-700'"
                            class="text-left p-4 rounded-2xl border transition-all duration-300 flex items-start space-x-4">
                            <div :class="activeTab === 'misi' ? 'bg-white/15 text-white' : 'bg-brand-50 text-brand-600'"
                                class="p-2 rounded-xl">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm sm:text-base">Misi Utama</h4>
                                <p :class="activeTab === 'misi' ? 'text-white/85' : 'text-slate-400'"
                                    class="text-xs mt-0.5">Langkah strategis berkelanjutan</p>
                            </div>
                        </button>

                        <button @click="activeTab = 'sejarah'"
                            :class="activeTab === 'sejarah' ? 'bg-brand-600 border-brand-600 text-white active-tab-glow' :
                                'bg-white border-slate-200/60 hover:border-slate-300 text-slate-700'"
                            class="text-left p-4 rounded-2xl border transition-all duration-300 flex items-start space-x-4">
                            <div :class="activeTab === 'sejarah' ? 'bg-white/15 text-white' : 'bg-brand-50 text-brand-600'"
                                class="p-2 rounded-xl">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm sm:text-base">Sejarah Singkat</h4>
                                <p :class="activeTab === 'sejarah' ? 'text-white/85' : 'text-slate-400'"
                                    class="text-xs mt-0.5">Perjalanan panjang inovasi</p>
                            </div>
                        </button>
                    </div>

                    {{-- Tab Content Panel --}}
                    <div
                        class="lg:col-span-8 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/50 min-h-[250px] flex flex-col justify-center">

                        {{-- Visi --}}
                        <div x-show="activeTab === 'visi'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4">
                            <span
                                class="text-xs font-bold text-brand-600 uppercase tracking-widest bg-brand-50 px-2.5 py-1 rounded">Masa
                                Depan Cerah</span>
                            <h3 class="text-2xl font-bold text-slate-900">Visi Kami</h3>
                            <div class="text-slate-600 leading-relaxed font-light prose prose-sm max-w-none">
                                {!! Setting::get(
                                    'visi',
                                    'Menjadi lembaga pendidikan unggulan yang mampu melahirkan generasi berkarakter, unggul dalam sains, berdaya cipta dalam teknologi, dengan landasan akhlak mulia.',
                                ) !!}
                            </div>
                        </div>

                        {{-- Misi --}}
                        <div x-show="activeTab === 'misi'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4"
                            style="display: none;">
                            <span
                                class="text-xs font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2.5 py-1 rounded">Rencana
                                Aksi</span>
                            <h3 class="text-2xl font-bold text-slate-900">Misi Kami</h3>
                            <div class="text-slate-600 leading-relaxed font-light prose prose-sm max-w-none">
                                {!! Setting::get(
                                    'misi',
                                    '<ol><li>Menyelenggarakan KBM yang inovatif melalui integrasi teknologi.</li><li>Menyediakan ekosistem kolaboratif inklusif bagi seluruh elemen akademis.</li><li>Menanamkan nilai akhlak melalui keterlibatan aktif sosial kemasyarakatan.</li></ol>',
                                ) !!}
                            </div>
                        </div>

                        {{-- Sejarah --}}
                        <div x-show="activeTab === 'sejarah'" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4"
                            style="display: none;">
                            <span
                                class="text-xs font-bold text-amber-600 uppercase tracking-widest bg-amber-50 px-2.5 py-1 rounded">{{ Setting::get('founded_year', 'Sejak 2018') }}</span>
                            <h3 class="text-2xl font-bold text-slate-900">Inovasi yang Tak Pernah Berhenti</h3>
                            <div class="text-slate-600 leading-relaxed font-light prose prose-sm max-w-none">
                                {!! Setting::get(
                                    'school_history',
                                    '<p>' .
                                        $school .
                                        ' didirikan dengan komitmen tinggi terhadap kualitas pendidikan. Selaras dengan perkembangan zaman, sekolah terus berinovasi untuk memberikan pengalaman belajar terbaik bagi para siswa.</p>',
                                ) !!}
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3: BLOG / BERITA --}}
    {{-- ============================================ --}}
    <section id="blog" class="py-24 relative overflow-hidden" x-data="{ filter: 'Semua', search: '' }">
        {{-- Background --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[20%] -right-[10%] w-[550px] h-[550px] bg-red-100/15 rounded-full blur-[140px]">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row items-start md:items-end justify-between mb-12 gap-6">
                <div class="space-y-3">
                    <span
                        class="text-xs font-black uppercase tracking-widest text-brand-600 bg-brand-50 px-3.5 py-1.5 rounded-full">
                        Kabar & Inspirasi
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                        Update Terbaru {{ $school }}
                    </h2>
                    <p class="text-slate-500 text-sm sm:text-base max-w-xl">
                        Temukan kabar prestasi, liputan agenda sekolah, dan cerita menarik dari komunitas kami.
                    </p>
                </div>

                {{-- Search --}}
                <div class="relative w-full md:w-80">
                    <input type="text" x-model="search" placeholder="Cari berita atau prestasi..."
                        class="w-full pl-11 pr-5 py-3 rounded-full border border-slate-200 bg-white shadow-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition text-sm text-slate-900">
                    <svg class="w-5 h-5 text-slate-400 absolute left-4 top-3.5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
            </div>

            {{-- Category Filter Chips --}}
            <div class="flex flex-wrap gap-2.5 mb-8">
                @foreach ($categories as $cat)
                    <button @click="filter = '{{ $cat }}'"
                        :class="filter === '{{ $cat }}' ? 'bg-brand-600 border-brand-600 text-white shadow-md' :
                            'bg-white border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50'"
                        class="px-5 py-2 rounded-full text-xs font-semibold border transition-all duration-300">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            {{-- News Grid --}}
            @if ($news->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($news as $item)
                        <article
                            x-show="(filter === 'Semua' || '{{ $item->category }}' === filter) && (search === '' || '{{ strtolower($item->title) }}'.includes(search.toLowerCase()) || '{{ strtolower($item->excerpt) }}'.includes(search.toLowerCase()))"
                            x-transition
                            class="bg-white rounded-3xl border border-slate-200/50 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col h-full group">
                            <a href="{{ route('news.show', $item->slug) }}" wire:navigate
                                class="flex flex-col h-full">
                                <div class="relative aspect-[4/3] overflow-hidden bg-slate-100 shrink-0">
                                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <span
                                        class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm text-xs font-bold text-brand-600 px-3 py-1 rounded-full shadow-sm">
                                        {{ $item->category }}
                                    </span>
                                </div>

                                <div class="p-6 flex flex-col flex-grow space-y-3">
                                    <div class="flex items-center space-x-2 text-xs text-slate-400 font-medium">
                                        <span>{{ $item->published_at?->translatedFormat('d M Y') }}</span>
                                        @if ($item->author)
                                            <span>&bull;</span>
                                            <span>{{ $item->author->name }}</span>
                                        @endif
                                    </div>
                                    <h3
                                        class="font-bold text-base text-slate-900 group-hover:text-brand-600 transition-colors line-clamp-2 leading-snug">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed font-light">
                                        {{ $item->excerpt }}
                                    </p>
                                    <div
                                        class="pt-3 mt-auto flex items-center text-xs font-bold text-brand-600 group-hover:translate-x-1.5 transition-transform duration-200">
                                        <span>Baca Selengkapnya</span>
                                        <svg class="w-4 h-4 ml-0.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <a href="{{ route('news.index') }}" wire:navigate
                        class="inline-flex items-center space-x-2 bg-white hover:bg-slate-50 text-slate-700 font-bold px-8 py-3.5 rounded-full text-sm border border-slate-200 shadow-sm apple-transition">
                        <span>Lihat Semua Berita</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            @else
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-100 max-w-md mx-auto">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Berita</h3>
                    <p class="text-sm text-slate-500">Berita akan segera ditampilkan di sini.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4: GURU / TIM PENGAJAR --}}
    {{-- ============================================ --}}
    <section id="teachers"
        class="py-24 bg-white border-b border-slate-100 relative overflow-hidden -mx-6 sm:-mx-8 lg:-mx-10 px-6 sm:px-8 lg:px-10">
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-[15%] left-[2%] w-28 h-28 border-[12px] border-brand-400/10 rounded-full hidden lg:block">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span
                    class="text-xs font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-full">
                    Tim Pengajar
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                    Guru Profesional & Berdedikasi
                </h2>
                <p class="text-base text-slate-500">
                    Para pendidik berpengalaman yang siap membimbing siswa meraih potensi terbaik mereka.
                </p>
            </div>

            {{-- Teachers Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($teachers->take(4) as $teacher)
                    <div
                        class="bg-white/80 backdrop-blur-sm rounded-[28px] p-6 border border-slate-100 hover:shadow-xl apple-transition hover:-translate-y-2 group text-center">
                        <div
                            class="w-20 h-20 rounded-full mx-auto mb-4 overflow-hidden ring-4 ring-brand-100 group-hover:ring-brand-300 transition-all">
                            <img src="{{ $teacher->photoUrl() }}" alt="{{ $teacher->name }}" loading="lazy"
                                decoding="async" class="w-full h-full object-cover">
                        </div>
                        <h3 class="font-bold text-slate-900 text-sm">{{ $teacher->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $teacher->position }}</p>
                        @if ($teacher->instagram || $teacher->facebook)
                            <div class="flex justify-center gap-2 mt-3">
                                @if ($teacher->instagram)
                                    <a href="{{ $teacher->instagram }}" target="_blank"
                                        class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 hover:bg-brand-100 hover:text-brand-600 transition">IG</a>
                                @endif
                                @if ($teacher->facebook)
                                    <a href="{{ $teacher->facebook }}" target="_blank"
                                        class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 hover:bg-brand-100 hover:text-brand-600 transition">FB</a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('teachers.index') }}" wire:navigate
                    class="bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold px-8 py-3.5 rounded-full text-sm inline-flex items-center space-x-2 transition duration-300 shadow-sm">
                    <span>Lihat Semua Guru</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION: GALLERY --}}
    {{-- ============================================ --}}
    @if ($albums->count())
        <section id="gallery" class="py-24 relative overflow-hidden -mx-6 sm:-mx-8 lg:-mx-10 px-6 sm:px-8 lg:px-10">
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute top-[10%] right-[5%] w-[480px] h-[480px] bg-purple-100/25 rounded-full blur-[130px]">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-12 space-y-3">
                    <span
                        class="text-xs font-black uppercase tracking-widest text-purple-600 bg-purple-50 px-3.5 py-1.5 rounded-full">
                        Galeri Kegiatan
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                        Momen Berharga di {{ $school }}
                    </h2>
                    <p class="text-base text-slate-500">
                        Kilas balik kegiatan, prestasi, dan kebersamaan yang membentuk komunitas kami.
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-5">
                    @foreach ($albums as $album)
                        <a href="{{ route('gallery.album', $album->slug) }}" wire:navigate
                            class="group relative overflow-hidden rounded-[24px] border border-slate-100 bg-white shadow-sm hover:shadow-xl apple-transition hover:-translate-y-1 block w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(25%-0.95rem)] max-w-sm">
                            <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                                <img src="{{ $album->coverUrl() }}" alt="{{ $album->title }}" loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent">
                            </div>
                            <div class="absolute bottom-0 inset-x-0 p-5">
                                <div
                                    class="inline-flex items-center gap-1.5 bg-white/95 backdrop-blur-sm text-purple-700 text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm mb-2">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    {{ $album->photos_count }} foto
                                </div>
                                <h3 class="text-white font-bold text-lg leading-tight drop-shadow">{{ $album->title }}
                                </h3>
                                @if ($album->description)
                                    <p class="text-white/85 text-xs mt-1 line-clamp-2 font-light">
                                        {{ $album->description }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION: BROSUR --}}
    {{-- ============================================ --}}
    @if ($brochures->count())
        <section id="brochures" class="py-20 relative overflow-hidden">
            <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute bottom-[10%] left-[2%] w-[420px] h-[420px] bg-amber-100/20 rounded-full blur-[120px]">
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10" x-data="{
                open: false,
                photos: [],
                idx: 0,
                show(images, start = 0) { this.photos = images;
                    this.idx = start;
                    this.open = true; },
                next() { if (this.photos.length) this.idx = (this.idx + 1) % this.photos.length; },
                prev() { if (this.photos.length) this.idx = (this.idx - 1 + this.photos.length) % this.photos.length; }
            }"
                @keydown.escape.window="open = false" @keydown.arrow-right.window="open && next()"
                @keydown.arrow-left.window="open && prev()">
                <div class="text-center max-w-3xl mx-auto mb-10 space-y-3">
                    <span
                        class="text-xs font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-3.5 py-1.5 rounded-full">
                        Brosur Resmi
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                        Informasi Lengkap Sekolah
                    </h2>
                    <p class="text-base text-slate-500">
                        Geser tiap kartu untuk melihat halaman tambahan, klik untuk pratinjau penuh.
                    </p>
                </div>

                <div class="flex flex-wrap justify-center gap-6">
                    @foreach ($brochures as $brochure)
                        @php
                            $imgs = $brochure->images;
                            $payload = $imgs
                                ->map(
                                    fn($i) => [
                                        'url' => $i->imageUrl(),
                                        'caption' => $brochure->title,
                                    ],
                                )
                                ->values()
                                ->all();
                            if (empty($payload) && $brochure->preview_image) {
                                $payload = [
                                    [
                                        'url' => asset('storage/' . $brochure->preview_image),
                                        'caption' => $brochure->title,
                                    ],
                                ];
                            }
                        @endphp
                        <div class="w-full sm:w-[calc(50%-0.75rem)] lg:w-[calc(25%-1.13rem)] max-w-sm flex flex-col"
                            x-data="{ slide: 0, total: {{ max(1, count($payload)) }} }">
                            <div
                                class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl apple-transition hover:-translate-y-1 overflow-hidden">
                                <div class="relative aspect-[3/4] overflow-hidden bg-slate-100 group">
                                    @if (!empty($payload))
                                        @foreach ($payload as $i => $p)
                                            <button type="button"
                                                @click="$dispatch('open-brochure', { images: @js($payload), start: slide })"
                                                x-show="slide === {{ $i }}"
                                                x-transition.opacity.duration.300ms
                                                class="absolute inset-0 w-full h-full">
                                                <img src="{{ $p['url'] }}" alt="{{ $brochure->title }}"
                                                    loading="lazy" decoding="async"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            </button>
                                        @endforeach
                                    @else
                                        <div
                                            class="absolute inset-0 w-full h-full flex items-center justify-center text-slate-400 text-xs">
                                            Tidak ada gambar
                                        </div>
                                    @endif

                                    @if (count($payload) > 1)
                                        <button type="button" @click.stop="slide = (slide - 1 + total) % total"
                                            class="absolute top-1/2 left-2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-700 flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition">
                                            ‹
                                        </button>
                                        <button type="button" @click.stop="slide = (slide + 1) % total"
                                            class="absolute top-1/2 right-2 -translate-y-1/2 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-slate-700 flex items-center justify-center shadow opacity-0 group-hover:opacity-100 transition">
                                            ›
                                        </button>
                                        <div
                                            class="absolute bottom-2 inset-x-0 flex items-center justify-center gap-1.5">
                                            @foreach ($payload as $i => $p)
                                                <button type="button" @click.stop="slide = {{ $i }}"
                                                    :class="slide === {{ $i }} ? 'bg-white w-5' :
                                                        'bg-white/60 w-1.5'"
                                                    class="h-1.5 rounded-full transition-all duration-300"></button>
                                            @endforeach
                                        </div>
                                        <span
                                            class="absolute top-2 right-2 bg-black/55 backdrop-blur text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                            <span x-text="slide + 1"></span>/<span x-text="total"></span>
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3 text-left">
                                    <h3 class="font-bold text-slate-900 text-sm line-clamp-1">{{ $brochure->title }}
                                    </h3>
                                    @if ($brochure->subtitle)
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                            {{ $brochure->subtitle }}</p>
                                    @endif
                                </div>
                            </div>
                            @if ($brochure->fileUrl())
                                <a href="{{ $brochure->fileUrl() }}" target="_blank" rel="noopener"
                                    class="mt-2 inline-flex items-center justify-center gap-1.5 w-full px-3 py-2 rounded-full bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    Unduh PDF
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Lightbox --}}
                <div x-show="open" x-cloak x-transition.opacity
                    @open-brochure.window="show($event.detail.images, $event.detail.start ?? 0)"
                    class="fixed inset-0 z-[60] bg-black/90 backdrop-blur flex items-center justify-center p-4"
                    @click.self="open = false">
                    <button type="button" @click="open = false"
                        class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl">✕</button>
                    <button type="button" @click="prev()" x-show="photos.length > 1"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center">‹</button>
                    <button type="button" @click="next()" x-show="photos.length > 1"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center">›</button>
                    <div class="max-w-3xl w-full text-center">
                        <template x-for="(p, i) in photos" :key="i">
                            <div x-show="i === idx" class="w-full">
                                <img :src="p.url" :alt="p.caption"
                                    class="max-h-[80vh] mx-auto rounded-xl object-contain shadow-2xl">
                                <p class="text-white/90 mt-3 text-sm" x-text="p.caption"></p>
                            </div>
                        </template>
                        <p class="text-white/50 text-xs mt-4" x-show="photos.length > 1">
                            <span x-text="idx + 1"></span> / <span x-text="photos.length"></span>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION 5: CONTACT US --}}
    {{-- ============================================ --}}
    <section id="contact" class="py-24 relative overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[20%] -left-[2%] w-[450px] h-[450px] bg-brand-100/20 rounded-full blur-[120px]">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch">

                {{-- Left: Contact Info --}}
                <div class="lg:col-span-5 flex flex-col justify-between space-y-8">
                    <div class="space-y-4">
                        <span
                            class="text-xs font-black uppercase tracking-widest text-brand-600 bg-brand-50 px-3.5 py-1.5 rounded-full">
                            Hubungi Kami
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900">
                            Siap Membantu Anda
                        </h2>
                        <p class="text-slate-500 leading-relaxed font-light">
                            Ada pertanyaan mengenai pendaftaran, program, atau fasilitas? Silakan hubungi kami.
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Address --}}
                        <div
                            class="bg-white/95 backdrop-blur-sm p-5 rounded-2xl border border-slate-200/50 flex items-start space-x-4 shadow-sm">
                            <div
                                class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">Alamat Sekolah</h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ Setting::get('address', 'Alamat belum diatur') }}</p>
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div
                            class="bg-white/95 backdrop-blur-sm p-5 rounded-2xl border border-slate-200/50 flex items-start space-x-4 shadow-sm">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.148.197-.324.38-.514.545a15.003 15.003 0 01-6.996-6.996c.165-.19.348-.366.545-.514l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">Telepon & WhatsApp</h4>
                                <p class="text-xs text-slate-500 mt-1">{{ Setting::get('phone', 'Belum diatur') }}</p>
                            </div>
                        </div>

                        {{-- Hours --}}
                        <div
                            class="bg-white/95 backdrop-blur-sm p-5 rounded-2xl border border-slate-200/50 flex items-start space-x-4 shadow-sm">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-900">Jam Operasional</h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ Setting::get('office_hours', 'Senin - Jumat: 08.00 - 15.00 WIB') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- PPDB CTA Banner --}}
                    <a href="{{ route('ppdb.create') }}" wire:navigate
                        class="bg-gradient-to-br from-brand-100 to-brand-200 border border-brand-200 rounded-[24px] h-36 flex flex-col justify-center items-center relative overflow-hidden p-6 text-center group">
                        <div
                            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-20 w-48 h-48 bg-brand-400 rounded-full blur-2xl">
                        </div>
                        <svg class="w-8 h-8 text-brand-600 mb-2 relative z-10 group-hover:rotate-12 transition-transform duration-300"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                        <h4
                            class="font-bold text-sm text-brand-900 relative z-10 flex items-center justify-center space-x-1">
                            <span>Daftar PPDB Sekarang</span>
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </h4>
                        <p class="text-xs text-brand-700/80 mt-1 relative z-10">Pendaftaran
                            {{ Setting::get('ppdb_year', '2026/2027') }} telah dibuka.</p>
                    </a>
                </div>

                {{-- Right: Contact Form --}}
                <div class="lg:col-span-7">
                    <div
                        class="bg-white/95 backdrop-blur-sm rounded-[32px] p-6 sm:p-10 border border-slate-200/60 google-shadow-md">
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Kirim Pesan Langsung</h3>
                        <p class="text-xs text-slate-400 mb-8">Tim kami akan merespons dalam waktu 1x24 jam
                            operasional.</p>

                        @livewire('public.contact-form')
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA: JADWALKAN KUNJUNGAN --}}
    {{-- ============================================ --}}
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div
                class="rounded-[28px] bg-gradient-to-r from-brand-400 to-brand-600 p-8 lg:p-10 flex flex-col lg:flex-row items-center gap-6 relative overflow-hidden">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2">
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/20 text-white shrink-0">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <div class="flex-1 text-white text-center lg:text-left">
                    <div class="text-xl font-bold">Jadwalkan Kunjungan Sekolah</div>
                    <div class="text-sm text-white/90 mt-1">Datang dan rasakan langsung lingkungan belajar yang modern
                        dan nyaman.</div>
                </div>
                <a href="{{ route('contact') }}#visit" wire:navigate
                    class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 hover:bg-slate-50 transition shadow-lg">
                    Atur Janji →
                </a>
            </div>
        </div>
    </section>
</div>
