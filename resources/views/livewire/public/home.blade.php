@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    // Set the last word of a translated heading in the display serif. Both halves are escaped.
    $accent = function (string $text): string {
        $pos = mb_strrpos($text, ' ');
        if ($pos === false) {
            return '<em class="accent-serif">' . e($text) . '</em>';
        }

        return e(mb_substr($text, 0, $pos)) . ' <em class="accent-serif">' . e(mb_substr($text, $pos + 1)) . '</em>';
    };
    $accentSchool = '<em class="accent-serif">' . e($school) . '</em>';
    $arrow = '<svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>';
@endphp
<div>
    @push('styles')
        <style>
            /* ===== Reveal on scroll (below the fold only) ===== */
            [data-aos="fade-up"] {
                opacity: 0;
                transform: translate3d(0, 24px, 0);
                transition:
                    opacity 700ms cubic-bezier(0.16, 1, 0.3, 1),
                    transform 700ms cubic-bezier(0.16, 1, 0.3, 1);
            }

            [data-aos="fade-up"].aos-show {
                opacity: 1;
                transform: none;
            }

            @media (prefers-reduced-motion: reduce) {
                [data-aos="fade-up"] {
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
                    const items = document.querySelectorAll('[data-aos="fade-up"]:not(.aos-bound)');
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
    <section id="home" class="relative flex min-h-[calc(100vh-200px)] items-center overflow-hidden py-10 lg:py-16">
        <div class="relative z-10 mx-auto w-full max-w-7xl px-2 sm:px-4 lg:px-6">
            <div class="grid grid-cols-1 items-center gap-14 lg:grid-cols-12 lg:gap-16">

                {{-- Left: Text & CTA --}}
                <div class="space-y-8 lg:col-span-7">
                    <div
                        class="inline-flex items-center gap-2.5 rounded-full border border-slate-200 bg-white py-1.5 pl-2.5 pr-4 text-[13px] font-medium text-slate-700">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-brand-500 ring-4 ring-brand-500/20"></span>
                        <span>{{ Setting::get('ppdb_badge', 'Penerimaan Siswa Baru TA ' . Setting::get('ppdb_year', '2026/2027') . ' Telah Dibuka') }}</span>
                    </div>

                    <h1
                        class="text-[2.75rem] font-bold leading-[1.04] tracking-[-0.035em] text-slate-900 sm:text-6xl xl:text-[4.5rem]">
                        {{ Setting::get('hero_title_1', 'Membentuk') }}
                        <em class="accent-serif">{{ Setting::get('hero_title_2', 'Masa Depan') }}</em>
                        {{ Setting::get('hero_title_3', 'yang Inovatif & Berkarakter') }}
                    </h1>

                    <p class="max-w-xl text-lg leading-relaxed text-slate-600">
                        {!! Setting::get(
                            'hero_subtitle',
                            'Selamat datang di ' .
                                $school .
                                '. Kami menghadirkan pendidikan akademis bertaraf internasional dengan ekosistem digital cerdas, kurikulum adaptif, dan pembinaan karakter berbasis nilai luhur.',
                        ) !!}
                    </p>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('ppdb.create') }}" wire:navigate class="btn-brand">
                            {{ __('Mulai Pendaftaran') }} {!! $arrow !!}
                        </a>
                        <a href="#about" class="btn-outline !px-7 !py-4 !text-[15px]">{{ __('Eksplorasi Profil') }}</a>
                    </div>

                    <dl class="grid max-w-lg grid-cols-3 border-t border-slate-200 pt-7">
                        <div class="flex flex-col-reverse justify-end gap-1.5 pr-4">
                            <dt class="text-[13px] text-slate-500">{{ __('Akreditasi') }}</dt>
                            <dd class="font-display text-3xl leading-none text-slate-900 sm:text-4xl">
                                {{ Setting::get('accreditation', 'Unggul (A)') }}</dd>
                        </div>
                        <div class="flex flex-col-reverse justify-end gap-1.5 border-l border-slate-200 px-4">
                            <dt class="text-[13px] text-slate-500">{{ __('Siswa Aktif') }}</dt>
                            <dd class="font-display text-3xl leading-none text-slate-900 sm:text-4xl">
                                {{ Setting::get('stat_students', '1200+') }}</dd>
                        </div>
                        <div class="flex flex-col-reverse justify-end gap-1.5 border-l border-slate-200 pl-4">
                            <dt class="text-[13px] text-slate-500">{{ __('Program Unggulan') }}</dt>
                            <dd class="font-display text-3xl leading-none text-slate-900 sm:text-4xl">
                                {{ Setting::get('stat_programs', '15+') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Right: Hero Image --}}
                <div class="lg:col-span-5">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <div class="relative aspect-[4/5] overflow-hidden rounded-[32px] bg-brand-100">
                            @if ($heroImage = Setting::imageUrl('hero_image'))
                                <img src="{{ $heroImage }}" alt="{{ $school }}" width="600" height="750"
                                    fetchpriority="high" class="h-full w-full object-cover">
                            @endif
                            <div
                                class="absolute inset-x-4 bottom-4 flex items-center justify-between gap-4 rounded-2xl bg-white/90 py-4 pl-5 pr-4 backdrop-blur-md">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-700">
                                        {{ Setting::get('hero_badge_label', 'Kolaborasi Aktif') }}</p>
                                    <p class="mt-0.5 text-[15px] font-semibold leading-snug text-slate-900">
                                        {{ Setting::get('hero_badge_text', 'Siswa Mengembangkan Proyek Inovatif') }}</p>
                                </div>
                                <a href="{{ route('programs.index') }}" wire:navigate aria-label="{{ __('Program') }}"
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-700 text-white transition hover:bg-brand-800">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Floating stats --}}
                        <div
                            class="absolute -left-3 top-8 w-44 rounded-2xl bg-white p-4 shadow-xl shadow-slate-900/10 sm:-left-6 sm:w-48 sm:p-5">
                            <div class="font-display text-4xl leading-none text-brand-700">
                                {{ Setting::get('stat_graduation') ?: '98%' }}</div>
                            <p class="mt-1.5 text-xs leading-snug text-slate-600">
                                {{ Setting::get('stat_graduation_label') ?: 'Lulusan Melanjutkan ke SMAN/SMKN' }}</p>
                        </div>
                        <div
                            class="absolute -right-3 top-[42%] w-40 rounded-2xl bg-white p-4 shadow-xl shadow-slate-900/10 sm:-right-6 sm:w-44 sm:p-5">
                            <div class="font-display text-4xl leading-none text-brand-700">
                                {{ Setting::get('stat_facility') ?: '100%' }}</div>
                            <p class="mt-1.5 text-xs leading-snug text-slate-600">
                                {{ Setting::get('stat_facility_label') ?: 'Fasilitas Modern & Lengkap' }}</p>
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
        class="relative -mx-6 border-y border-slate-200 bg-white px-6 py-24 sm:-mx-8 sm:px-8 lg:-mx-10 lg:px-10 lg:py-28">
        <div class="mx-auto max-w-7xl space-y-16 px-2 sm:px-4 lg:px-6">
            <div data-aos="fade-up" class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end lg:gap-12">
                <div class="max-w-2xl space-y-5">
                    <p class="eyebrow">{{ __('Pilar Pendidikan Kami') }}</p>
                    <h2 class="section-title">{!! $accent(__('Membangun Fondasi Masa Depan yang Kokoh')) !!}</h2>
                </div>
                <p class="max-w-md text-base leading-relaxed text-slate-600">
                    {{ Setting::get('about_subtitle', $school . ' didirikan untuk menjadi pionir pendidikan terintegrasi teknologi, tanpa melupakan penanaman moral yang luhur.') }}
                </p>
            </div>

            {{-- Three Pillars --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach ($programs->take(3) as $index => $program)
                    <a href="{{ route('programs.show', $program->slug) }}" wire:navigate data-aos="fade-up"
                        data-aos-delay="{{ $index * 120 }}"
                        class="group flex min-h-[17rem] flex-col gap-3.5 rounded-3xl bg-slate-50 p-8 transition duration-300 ease-ios hover:-translate-y-1 hover:bg-brand-50">
                        <span class="font-display text-[2.75rem] leading-none text-brand-700">{{ sprintf('%02d', $index + 1) }}</span>
                        <h3 class="mt-auto text-[22px] font-semibold tracking-[-0.015em] text-slate-900">{{ $program->title }}</h3>
                        <p class="text-[15px] leading-relaxed text-slate-600">{{ $program->short_description }}</p>
                        <span class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-brand-700">
                            {{ __('Lihat Detail') }}
                            <svg class="h-3.5 w-3.5 transition-transform group-hover:translate-x-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Visi Misi Sejarah Tabs --}}
            <div data-aos="fade-up" x-data="{ activeTab: 'visi' }"
                class="grid grid-cols-1 gap-8 rounded-[32px] bg-slate-50 p-4 sm:p-10 lg:grid-cols-12">
                <div class="flex flex-col gap-2 lg:col-span-4">
                    <h3 class="mb-3 px-2 text-2xl font-bold tracking-[-0.02em] text-slate-900 sm:px-0">
                        {{ __('Profil Inti Sekolah') }}</h3>
                    @foreach ([['visi', 'Visi Sekolah', 'Tujuan akhir & impian bersama'], ['misi', 'Misi Utama', 'Langkah strategis berkelanjutan'], ['sejarah', 'Sejarah Singkat', 'Perjalanan panjang inovasi']] as [$key, $label, $hint])
                        <button type="button" @click="activeTab = '{{ $key }}'"
                            :aria-pressed="activeTab === '{{ $key }}'"
                            :class="activeTab === '{{ $key }}' ? 'bg-white border-slate-200 shadow-sm' :
                                'border-transparent hover:bg-white'"
                            class="flex w-full items-start gap-4 rounded-2xl border p-4 text-left transition duration-300 ease-ios">
                            <span :class="activeTab === '{{ $key }}' ? 'text-brand-700' : 'text-slate-400'"
                                class="font-display text-[22px] leading-tight transition-colors">{{ sprintf('%02d', $loop->iteration) }}</span>
                            <span>
                                <span class="block text-[15px] font-semibold text-slate-900">{{ __($label) }}</span>
                                <span class="block text-[13px] text-slate-500">{{ __($hint) }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                <div
                    class="flex min-h-[18rem] flex-col justify-center rounded-3xl border border-slate-200 bg-white p-6 sm:p-10 lg:col-span-8">
                    <div x-show="activeTab === 'visi'" x-transition:enter="transition ease-ios duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2" class="space-y-5">
                        <p class="eyebrow">{{ __('Visi Kami') }}</p>
                        <div class="font-display text-2xl leading-snug text-slate-900 sm:text-[2rem]">
                            {!! Setting::get(
                                'visi',
                                'Menjadi lembaga pendidikan unggulan yang mampu melahirkan generasi berkarakter, unggul dalam sains, berdaya cipta dalam teknologi, dengan landasan akhlak mulia.',
                            ) !!}
                        </div>
                    </div>

                    <div x-show="activeTab === 'misi'" x-transition:enter="transition ease-ios duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2" class="space-y-5" style="display: none;">
                        <p class="eyebrow">{{ __('Misi Kami') }}</p>
                        <div class="prose max-w-none text-[17px] leading-relaxed text-slate-600">
                            {!! Setting::get(
                                'misi',
                                '<ol><li>Menyelenggarakan KBM yang inovatif melalui integrasi teknologi.</li><li>Menyediakan ekosistem kolaboratif inklusif bagi seluruh elemen akademis.</li><li>Menanamkan nilai akhlak melalui keterlibatan aktif sosial kemasyarakatan.</li></ol>',
                            ) !!}
                        </div>
                    </div>

                    <div x-show="activeTab === 'sejarah'" x-transition:enter="transition ease-ios duration-300"
                        x-transition:enter-start="opacity-0 translate-y-2" class="space-y-5" style="display: none;">
                        <p class="eyebrow">{{ Setting::get('founded_year', 'Sejak 2018') }}</p>
                        <h3 class="text-2xl font-bold tracking-[-0.02em] text-slate-900 sm:text-[1.75rem]">
                            {{ __('Inovasi yang Tak Pernah Berhenti') }}</h3>
                        <div class="prose max-w-none text-[17px] leading-relaxed text-slate-600">
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
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 3: BLOG / BERITA --}}
    {{-- ============================================ --}}
    <section id="blog" class="relative py-24 lg:py-28" x-data="{
        filter: 'Semua',
        search: '',
        get filtering() { return this.filter !== 'Semua' || this.search.trim() !== '' },
        matches(category, text) {
            return (this.filter === 'Semua' || category === this.filter) &&
                text.includes(this.search.trim().toLowerCase())
        },
    }">
        <div class="mx-auto max-w-7xl space-y-10 px-2 sm:px-4 lg:px-6">
            <div data-aos="fade-up" class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end lg:gap-12">
                <div class="max-w-2xl space-y-5">
                    <p class="eyebrow">{{ __('Kabar & Inspirasi') }}</p>
                    <h2 class="section-title">{!! __('Update Terbaru :school', ['school' => $accentSchool]) !!}</h2>
                    <p class="max-w-xl text-base leading-relaxed text-slate-600">
                        {{ __('Temukan kabar prestasi, liputan agenda sekolah, dan cerita menarik dari komunitas kami.') }}
                    </p>
                </div>

                <div class="relative w-full shrink-0 lg:w-80">
                    <label for="news-search" class="sr-only">{{ __('Cari berita atau prestasi...') }}</label>
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-slate-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input id="news-search" type="search" x-model="search"
                        placeholder="{{ __('Cari berita atau prestasi...') }}"
                        class="h-12 w-full rounded-full border border-slate-200 bg-white pl-11 pr-5 text-sm text-slate-900 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200">
                </div>
            </div>

            {{-- Category Filter Chips --}}
            <div class="flex flex-wrap gap-2">
                @foreach ($categories as $cat)
                    <button type="button" @click="filter = @js($cat)" :aria-pressed="filter === @js($cat)"
                        :class="filter === @js($cat) ? 'bg-brand-700 border-brand-700 text-white' :
                            'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
                        class="h-10 rounded-full border px-[18px] text-[13px] font-semibold transition duration-200">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>

            @if ($news->count() > 0)
                @php $lead = $news->first(); @endphp
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
                    {{-- Featured story: shown only while no filter or search is active --}}
                    <a href="{{ route('news.show', $lead->slug) }}" wire:navigate x-show="!filtering"
                        class="group flex flex-col gap-5 lg:col-span-7">
                        <div class="relative aspect-[16/10] overflow-hidden rounded-3xl bg-slate-100">
                            <img src="{{ $lead->imageUrl() }}" alt="{{ $lead->title }}" loading="lazy" decoding="async"
                                class="h-full w-full object-cover transition duration-700 ease-ios group-hover:scale-[1.03]">
                            <span
                                class="absolute left-4 top-4 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-brand-700">{{ $lead->category }}</span>
                        </div>
                        <div class="space-y-2.5">
                            <p class="text-[13px] text-slate-500">
                                {{ $lead->published_at?->translatedFormat('d M Y') }}
                                @if ($lead->author)
                                    &middot; {{ $lead->author->name }}
                                @endif
                            </p>
                            <h3
                                class="text-2xl font-bold leading-tight tracking-[-0.02em] text-slate-900 transition group-hover:text-brand-700 sm:text-[1.875rem]">
                                {{ $lead->title }}</h3>
                            <p class="line-clamp-3 text-base leading-relaxed text-slate-600">{{ $lead->excerpt }}</p>
                            <span class="inline-flex items-center gap-1.5 pt-1 text-sm font-semibold text-brand-700">
                                {{ __('Baca Selengkapnya') }} {!! $arrow !!}
                            </span>
                        </div>
                    </a>

                    {{-- Story list: the rest, or every match once filtering --}}
                    <div x-cloak
                        :class="filtering ? 'grid gap-x-10 md:grid-cols-2 lg:col-span-12' : 'flex flex-col lg:col-span-5'">
                        @foreach ($news as $item)
                            <a href="{{ route('news.show', $item->slug) }}" wire:navigate
                                x-show="matches(@js($item->category), @js(Str::lower($item->title . ' ' . $item->excerpt))) && {{ $loop->first ? 'filtering' : 'true' }}"
                                class="group flex items-center gap-5 border-b border-slate-200 py-5">
                                <div class="aspect-[4/3] w-28 shrink-0 overflow-hidden rounded-2xl bg-slate-100 sm:w-32">
                                    <img src="{{ $item->imageUrl() }}" alt="" loading="lazy" decoding="async"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                </div>
                                <div class="min-w-0 space-y-1.5">
                                    <p class="text-xs text-slate-500">
                                        <span class="font-semibold text-brand-700">{{ $item->category }}</span>
                                        &middot; {{ $item->published_at?->translatedFormat('d M Y') }}
                                    </p>
                                    <h3
                                        class="line-clamp-2 text-[17px] font-semibold leading-snug tracking-[-0.01em] text-slate-900 transition group-hover:text-brand-700">
                                        {{ $item->title }}</h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <a href="{{ route('news.index') }}" wire:navigate class="btn-outline">
                        {{ __('Lihat Semua Berita') }} {!! $arrow !!}
                    </a>
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-slate-200 px-6 py-14 text-center">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('Belum Ada Berita') }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ __('Berita akan segera ditampilkan di sini.') }}</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION 4: GURU / TIM PENGAJAR --}}
    {{-- ============================================ --}}
    <section id="teachers"
        class="relative -mx-6 border-y border-slate-200 bg-white px-6 py-24 sm:-mx-8 sm:px-8 lg:-mx-10 lg:px-10 lg:py-28">
        <div class="mx-auto max-w-7xl space-y-14 px-2 sm:px-4 lg:px-6">
            <div data-aos="fade-up" class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end lg:gap-12">
                <div class="max-w-2xl space-y-5">
                    <p class="eyebrow">{{ __('Tim Pengajar') }}</p>
                    <h2 class="section-title">{!! $accent(__('Guru Profesional & Berdedikasi')) !!}</h2>
                    <p class="max-w-xl text-base leading-relaxed text-slate-600">
                        {{ __('Para pendidik berpengalaman yang siap membimbing siswa meraih potensi terbaik mereka.') }}
                    </p>
                </div>
                <a href="{{ route('teachers.index') }}" wire:navigate class="btn-outline shrink-0 self-start lg:self-auto">
                    {{ __('Lihat Semua Guru') }} {!! $arrow !!}
                </a>
            </div>

            <div class="grid grid-cols-2 gap-x-5 gap-y-10 lg:grid-cols-4">
                @foreach ($teachers->take(4) as $teacher)
                    <div data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}" class="group">
                        <div class="aspect-[4/5] overflow-hidden rounded-3xl bg-brand-50">
                            <img src="{{ $teacher->photoUrl() }}" alt="{{ $teacher->name }}" loading="lazy"
                                decoding="async"
                                class="h-full w-full object-cover transition duration-700 ease-ios group-hover:scale-[1.03]">
                        </div>
                        <h3 class="mt-4 text-[17px] font-semibold tracking-[-0.01em] text-slate-900">{{ $teacher->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $teacher->position }}</p>
                        @if ($teacher->instagram || $teacher->facebook)
                            <div class="mt-2 flex gap-3 text-xs font-semibold">
                                @if ($teacher->instagram)
                                    <a href="{{ $teacher->instagram }}" target="_blank" rel="noopener"
                                        class="text-slate-500 transition hover:text-brand-700">Instagram</a>
                                @endif
                                @if ($teacher->facebook)
                                    <a href="{{ $teacher->facebook }}" target="_blank" rel="noopener"
                                        class="text-slate-500 transition hover:text-brand-700">Facebook</a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================ --}}
    {{-- SECTION: GALLERY --}}
    {{-- ============================================ --}}
    @if ($albums->count())
        @php
            $albumCount = $albums->count();
            // Bento: first album large; the rest fill the remaining cells for 1–4 albums.
            $tile = fn (int $i): string => match (true) {
                $i === 0 && $albumCount === 1 => 'sm:col-span-2 lg:col-span-3 lg:row-span-2',
                $i === 0 => 'sm:col-span-2 lg:row-span-2',
                $albumCount === 2 => 'lg:row-span-2',
                $albumCount === 4 && $i === 3 => 'sm:col-span-2 lg:col-span-3',
                default => '',
            };
        @endphp
        <section id="gallery" class="relative py-24 lg:py-28">
            <div class="mx-auto max-w-7xl space-y-12 px-2 sm:px-4 lg:px-6">
                <div data-aos="fade-up" class="max-w-2xl space-y-5">
                    <p class="eyebrow">{{ __('Galeri Kegiatan') }}</p>
                    <h2 class="section-title">{!! __('Momen Berharga di :school', ['school' => $accentSchool]) !!}</h2>
                    <p class="text-base leading-relaxed text-slate-600">
                        {{ __('Kilas balik kegiatan, prestasi, dan kebersamaan yang membentuk komunitas kami.') }}
                    </p>
                </div>

                <div class="grid auto-rows-[15rem] grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($albums as $album)
                        <a href="{{ route('gallery.album', $album->slug) }}" wire:navigate
                            class="group relative block overflow-hidden rounded-[28px] bg-slate-100 {{ $tile($loop->index) }}">
                            <img src="{{ $album->coverUrl() }}" alt="{{ $album->title }}" loading="lazy"
                                decoding="async"
                                class="absolute inset-0 h-full w-full object-cover transition duration-700 ease-ios group-hover:scale-[1.03]">
                            <div class="absolute inset-x-4 bottom-4 rounded-2xl bg-white/95 px-5 py-3.5">
                                <p class="text-xs font-semibold text-brand-700">
                                    {{ __(':count foto', ['count' => $album->photos_count]) }}</p>
                                <h3 class="truncate text-base font-semibold text-slate-900 sm:text-lg">{{ $album->title }}</h3>
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
        <section id="brochures" class="relative -mx-6 border-y border-slate-200 bg-white px-6 py-24 sm:-mx-8 sm:px-8 lg:-mx-10 lg:px-10">
            <div class="mx-auto max-w-7xl space-y-12 px-2 sm:px-4 lg:px-6" x-data="{
                open: false,
                photos: [],
                idx: 0,
                lightFade: true,
                show(images, start = 0) {
                    this.photos = images;
                    this.idx = start;
                    this.lightFade = true;
                    this.open = true;
                },
                swap(to) {
                    if (!this.photos.length || to === this.idx) return;
                    this.lightFade = false;
                    const img = new Image();
                    img.src = this.photos[to].full;
                    const apply = () => { this.idx = to;
                        requestAnimationFrame(() => { this.lightFade = true; }); };
                    img.complete ? apply() : (img.onload = apply, img.onerror = apply);
                },
                next() { if (this.photos.length) this.swap((this.idx + 1) % this.photos.length); },
                prev() { if (this.photos.length) this.swap((this.idx - 1 + this.photos.length) % this.photos.length); },
                // Geser kiri/kanan untuk pindah halaman; `swiped` menahan klik yang
                // menyusul supaya tirai tidak ikut tertutup.
                swiped: false,
                sx: null,
                down(e) { this.sx = e.clientX; this.swiped = false; },
                up(e) {
                    if (this.sx === null) return;
                    const dx = e.clientX - this.sx;
                    this.sx = null;
                    if (Math.abs(dx) < 40) return;
                    this.swiped = true;
                    dx < 0 ? this.next() : this.prev();
                }
            }"
                @keydown.escape.window="open = false" @keydown.arrow-right.window="open && next()"
                @keydown.arrow-left.window="open && prev()">
                <div data-aos="fade-up" class="max-w-2xl space-y-5">
                    <p class="eyebrow">{{ __('Brosur Resmi') }}</p>
                    <h2 class="section-title">{!! $accent(__('Informasi Lengkap Sekolah')) !!}</h2>
                    <p class="text-base leading-relaxed text-slate-600">
                        {{ __('Geser tiap kartu untuk melihat halaman tambahan, klik untuk pratinjau penuh.') }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 sm:gap-6 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($brochures as $brochure)
                        @php
                            $imgs = $brochure->images;
                            $payload = $imgs
                                ->map(
                                    fn($i) => [
                                        'full' => $i->imageUrl(),
                                        'thumb' => $i->thumbnailUrl(),
                                        'caption' => $brochure->title,
                                    ],
                                )
                                ->values()
                                ->all();
                            if (empty($payload) && $brochure->preview_image) {
                                $payload = [
                                    [
                                        'full' => asset('storage/' . $brochure->preview_image),
                                        'thumb' => asset('storage/' . $brochure->preview_image),
                                        'caption' => $brochure->title,
                                    ],
                                ];
                            }
                        @endphp
                        <div class="flex flex-col" x-data="brochureCard(@js($payload))">
                            {{-- Brosur adalah dokumen: tampilkan utuh di atas alas, jangan dipotong.
                                 Rasio 4:5 cukup untuk halaman tegak (A4) maupun melebar. --}}
                            <div class="group relative aspect-[4/5] overflow-hidden rounded-2xl bg-slate-100 sm:rounded-3xl">
                                <template x-if="items.length">
                                    <button type="button" aria-label="{{ __('Pratinjau penuh') }}"
                                        @pointerdown="down($event)" @pointerup="up($event)"
                                        @click="swiped ? (swiped = false) : $dispatch('open-brochure', { images: items, start: slide })"
                                        :class="total > 1 ? 'pb-10' : ''"
                                        class="absolute inset-0 h-full w-full touch-pan-y select-none p-3 sm:p-4">
                                        <img :src="items[slide].thumb" :alt="items[slide].caption" loading="lazy"
                                            decoding="async" draggable="false"
                                            class="h-full w-full object-contain drop-shadow-md transition-[opacity,transform] duration-500 ease-out group-hover:scale-[1.02]"
                                            :style="`opacity:${fade ? 1 : 0}`">
                                    </button>
                                </template>
                                <template x-if="!items.length">
                                    @if ($brochure->fileUrl())
                                        <a href="{{ $brochure->fileUrl() }}" target="_blank" rel="noopener"
                                            class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-500 transition hover:text-brand-700">
                                            <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                stroke-width="1.4" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                            <span class="text-xs font-semibold uppercase tracking-[0.14em]">PDF</span>
                                        </a>
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center text-xs text-slate-500">
                                            {{ __('Tidak ada gambar') }}
                                        </div>
                                    @endif
                                </template>

                                <template x-if="total > 1">
                                    <div>
                                        {{-- Panah hanya di perangkat ber-hover; di layar sentuh: geser + titik halaman. --}}
                                        <button type="button" @click.stop="prev()" aria-label="{{ __('Halaman sebelumnya') }}"
                                            class="absolute left-2 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow opacity-0 transition hover:bg-white group-hover:opacity-100 active:scale-90 [@media(hover:hover)]:flex">
                                            ‹
                                        </button>
                                        <button type="button" @click.stop="next()" aria-label="{{ __('Halaman berikutnya') }}"
                                            class="absolute right-2 top-1/2 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full bg-white/90 text-slate-700 shadow opacity-0 transition hover:bg-white group-hover:opacity-100 active:scale-90 [@media(hover:hover)]:flex">
                                            ›
                                        </button>
                                        <div class="absolute inset-x-0 bottom-1.5 flex items-center justify-center">
                                            <template x-for="(p, i) in items" :key="i">
                                                <button type="button" @click.stop="go(i)"
                                                    :aria-label="`{{ __('Halaman') }} ${i + 1}`"
                                                    :aria-current="slide === i ? 'true' : null"
                                                    class="flex h-7 min-w-7 items-center justify-center px-0.5">
                                                    <span :class="slide === i ? 'w-5 bg-slate-900 dark:bg-white' : 'w-1.5 bg-slate-400/70'"
                                                        class="block h-1.5 rounded-full transition-all duration-300"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-sm font-semibold leading-snug text-slate-900 sm:mt-4 sm:text-[15px]">{{ $brochure->title }}</h3>
                            @if ($brochure->subtitle)
                                <p class="line-clamp-1 text-xs text-slate-500 sm:text-sm">{{ $brochure->subtitle }}</p>
                            @endif
                            @if ($brochure->fileUrl())
                                <a href="{{ $brochure->fileUrl() }}" target="_blank" rel="noopener"
                                    class="mt-2 inline-flex items-center gap-1.5 self-start text-[13px] font-semibold text-brand-700 transition hover:text-brand-800">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                    </svg>
                                    {{ __('Unduh PDF') }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Lightbox dipindah ke <body>: wadah <main> memakai backdrop-filter, yang
                     membuat position:fixed mengacu ke wadah itu (bukan viewport), sehingga
                     tirai hanya menutup kartu dan gambarnya terdorong jauh di luar layar. --}}
                <template x-teleport="body">
                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        @open-brochure.window="show($event.detail.images, $event.detail.start ?? 0)"
                        @pointerdown="down($event)" @pointerup="up($event)"
                        @click.self="swiped ? (swiped = false) : (open = false)"
                        role="dialog" aria-modal="true" aria-label="{{ __('Pratinjau brosur') }}"
                        class="fixed inset-0 z-[70] flex touch-pan-y select-none items-center justify-center bg-black/90 p-3 backdrop-blur-sm sm:p-6">
                        <button type="button" @click="open = false" aria-label="{{ __('Tutup') }}"
                            class="absolute right-3 top-3 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20 active:scale-90 sm:right-4 sm:top-4">✕</button>
                        <button type="button" @click="prev()" x-show="photos.length > 1" aria-label="{{ __('Halaman sebelumnya') }}"
                            class="absolute left-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20 active:scale-90 sm:left-4">‹</button>
                        <button type="button" @click="next()" x-show="photos.length > 1" aria-label="{{ __('Halaman berikutnya') }}"
                            class="absolute right-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20 active:scale-90 sm:right-4">›</button>
                        <div class="w-full max-w-5xl px-12 text-center sm:px-16" x-show="photos.length">
                            <img :src="photos[idx]?.full" :alt="photos[idx]?.caption" draggable="false"
                                class="mx-auto max-h-[78vh] w-auto max-w-full rounded-lg object-contain shadow-2xl transition-opacity duration-300 sm:max-h-[82vh]"
                                :style="`opacity:${lightFade ? 1 : 0}`">
                            <p class="mt-3 text-sm text-white/90" x-text="photos[idx]?.caption"></p>
                            <p class="mt-1.5 text-xs text-white/60" x-show="photos.length > 1">
                                <span x-text="idx + 1"></span> / <span x-text="photos.length"></span>
                            </p>
                        </div>
                    </div>
                </template>
            </div>
        </section>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                // Lightweight per-card slider with a single <img> and a quick cross-fade.
                Alpine.data('brochureCard', (items) => ({
                    items: items || [],
                    slide: 0,
                    fade: true,
                    get total() {
                        return this.items.length;
                    },
                    swap(to) {
                        if (to === this.slide || !this.total) return;
                        this.fade = false;
                        // Preload next image before swapping for a smooth fade.
                        const img = new Image();
                        img.src = this.items[to].thumb;
                        const apply = () => {
                            this.slide = to;
                            requestAnimationFrame(() => {
                                this.fade = true;
                            });
                        };
                        img.complete ? apply() : (img.onload = apply, img.onerror = apply);
                    },
                    next() {
                        this.swap((this.slide + 1) % this.total);
                    },
                    prev() {
                        this.swap((this.slide - 1 + this.total) % this.total);
                    },
                    go(i) {
                        this.swap(i);
                    },
                    // Geser kiri/kanan untuk pindah halaman; `swiped` menahan klik yang
                    // menyusul supaya lightbox tidak ikut terbuka.
                    swiped: false,
                    sx: null,
                    down(e) {
                        this.sx = e.clientX;
                        this.swiped = false;
                    },
                    up(e) {
                        if (this.sx === null) return;
                        const dx = e.clientX - this.sx;
                        this.sx = null;
                        if (Math.abs(dx) < 40) return;
                        this.swiped = true;
                        dx < 0 ? this.next() : this.prev();
                    },
                }));
            });
        </script>
    @endpush

    {{-- ============================================ --}}
    {{-- SECTION 5: CONTACT US --}}
    {{-- ============================================ --}}
    <section id="contact" class="relative py-24 lg:py-28">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-14 px-2 sm:px-4 lg:grid-cols-12 lg:px-6">

            {{-- Left: Contact Info --}}
            <div class="flex flex-col gap-9 lg:col-span-5">
                <div data-aos="fade-up" class="space-y-5">
                    <p class="eyebrow">{{ __('Hubungi Kami') }}</p>
                    <h2 class="section-title">{!! $accent(__('Siap Membantu Anda')) !!}</h2>
                    <p class="text-base leading-relaxed text-slate-600">
                        {{ __('Ada pertanyaan mengenai pendaftaran, program, atau fasilitas? Silakan hubungi kami.') }}
                    </p>
                </div>

                @php
                    $contactRows = [
                        [
                            __('Alamat Sekolah'),
                            Setting::get('address', 'Alamat belum diatur'),
                            'M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z',
                        ],
                        [
                            __('Telepon & WhatsApp'),
                            Setting::get('phone', 'Belum diatur'),
                            'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.08-7.074-6.996l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z',
                        ],
                        [
                            __('Jam Operasional'),
                            Setting::get('office_hours', 'Senin - Jumat: 08.00 - 15.00 WIB'),
                            'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                        ],
                    ];
                @endphp
                <ul class="border-t border-slate-200">
                    @foreach ($contactRows as [$label, $value, $icon])
                        <li class="flex items-start gap-4 border-b border-slate-200 py-5">
                            <span
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-brand-50 text-brand-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-[15px] font-semibold text-slate-900">{{ $label }}</p>
                                <p class="text-sm text-slate-600">{{ $value }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- PPDB CTA --}}
                <a href="{{ route('ppdb.create') }}" wire:navigate
                    class="group flex items-center justify-between gap-5 rounded-3xl bg-brand-50 p-6 pl-7 transition hover:bg-brand-100">
                    <span>
                        <span class="block text-[17px] font-bold tracking-[-0.01em] text-slate-900">{{ __('Daftar PPDB Sekarang') }}</span>
                        <span class="block text-sm text-slate-600">{{ __('Pendaftaran :year telah dibuka.', ['year' => Setting::get('ppdb_year', '2026/2027')]) }}</span>
                    </span>
                    <span
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-700 text-white transition group-hover:translate-x-1">
                        {!! $arrow !!}
                    </span>
                </a>
            </div>

            {{-- Right: Contact Form --}}
            <div class="lg:col-span-7">
                <div class="rounded-[32px] border border-slate-200 bg-white p-6 shadow-xl shadow-slate-900/5 sm:p-10">
                    <h3 class="text-2xl font-bold tracking-[-0.02em] text-slate-900">{{ __('Kirim Pesan Langsung') }}</h3>
                    <p class="mb-8 mt-1 text-sm text-slate-500">{{ __('Tim kami akan merespons dalam waktu 1x24 jam operasional.') }}</p>

                    @livewire('public.contact-form')
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================ --}}
    {{-- CTA: JADWALKAN KUNJUNGAN --}}
    {{-- ============================================ --}}
    <section class="pb-4">
        <div
            class="relative mx-auto max-w-7xl overflow-hidden rounded-[36px] bg-brand-950 p-8 text-white sm:p-12 lg:p-16">
            <span aria-hidden="true"
                class="pointer-events-none absolute -right-32 -top-40 h-[30rem] w-[30rem] rounded-full border border-white/10"></span>
            <span aria-hidden="true"
                class="pointer-events-none absolute -right-10 -top-20 h-80 w-80 rounded-full border border-white/10"></span>
            <div class="relative flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-2xl space-y-4">
                    <h2
                        class="text-[2rem] font-bold leading-[1.1] tracking-[-0.03em] sm:text-4xl lg:text-[2.875rem] [&_.accent-serif]:text-brand-200">
                        {!! $accent(__('Jadwalkan Kunjungan Sekolah')) !!}</h2>
                    <p class="text-base leading-relaxed text-white/75">
                        {{ __('Datang dan rasakan langsung lingkungan belajar yang modern dan nyaman.') }}</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('contact') }}#visit" wire:navigate
                        class="inline-flex items-center justify-center gap-2.5 rounded-full bg-white px-7 py-4 text-[15px] font-semibold text-brand-900 transition hover:bg-brand-50 active:scale-[0.97]">
                        <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        {{ __('Atur Janji') }}
                    </a>
                    <a href="{{ route('ppdb.create') }}" wire:navigate
                        class="inline-flex items-center justify-center gap-2.5 rounded-full border border-white/30 px-7 py-4 text-[15px] font-semibold text-white transition hover:bg-white/10 active:scale-[0.97]">
                        PPDB {{ Setting::get('ppdb_year', '2026') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
