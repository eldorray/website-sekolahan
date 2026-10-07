<!DOCTYPE html>
<html lang="id" class="">

<head>
    <meta charset="UTF-8">
    <script>
        (function() {
            function applyTheme() {
                try {
                    var theme = localStorage.getItem('theme');
                    var isDark = theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)')
                        .matches);
                    document.documentElement.classList.toggle('dark', isDark);
                } catch (e) {}
            }
            applyTheme();
            document.addEventListener('livewire:navigated', applyTheme);
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php $seo = app(\App\Support\Seo::class); @endphp
    <title>{{ $title ?? $seo->fullTitle() }}</title>
    <meta name="description" content="{{ $seo->metaDescription() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- Open Graph / Twitter --}}
    <meta property="og:site_name" content="{{ $seo->schoolName() }}">
    <meta property="og:title" content="{{ $seo->fullTitle() }}">
    <meta property="og:description" content="{{ $seo->metaDescription() }}">
    <meta property="og:type" content="{{ $seo->type }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage = $seo->ogImage())
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo->fullTitle() }}">
    <meta name="twitter:description" content="{{ $seo->metaDescription() }}">
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- Variable sans (one file for 400-800) + display serif used only for accent words and numerals. --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400..800&display=swap">
    @vite(['resources/css/app.css'])
    @livewireStyles
    @php
        $eventTheme = \App\Models\EventTheme::activeFor(now());
        $eventBackgroundImageUrl = $eventTheme?->backgroundImageUrl();
    @endphp
    <x-brand-styles :event-theme="$eventTheme" />
    @stack('styles')
    <style>
        .liquid-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
        }

        .liquid-glass-dark {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }

        .apple-transition {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .bg-main {
            background-image: linear-gradient(160deg, #eef4ea 0%, #f6f8f5 45%, #e8eef4 100%);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            opacity: 0;
            animation: fadeUp .5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @media (prefers-reduced-motion: reduce) {

            .animate-fade-up,
            .animate-float {
                animation: none;
                opacity: 1;
            }

            .apple-transition {
                transition: none;
            }
        }

        body.has-event-theme .event-background {
            background-color: var(--event-background);
            background-image:
                linear-gradient(135deg, var(--event-background) 0%, #ffffff 78%),
                repeating-linear-gradient(45deg, color-mix(in srgb, var(--event-primary) 14%, transparent) 0 1px, transparent 1px 18px);
        }

        body.has-event-theme .event-background-overlay {
            background: color-mix(in srgb, var(--event-background) 72%, white 28%);
        }

        body.event-theme-independence .event-background {
            background-image:
                linear-gradient(180deg, color-mix(in srgb, var(--event-primary) 24%, white) 0 48%, #ffffff 48% 100%),
                repeating-linear-gradient(90deg, color-mix(in srgb, var(--event-primary) 18%, transparent) 0 2px, transparent 2px 26px);
        }

        body.event-theme-ramadan .event-background,
        body.event-theme-eid-fitr .event-background,
        body.event-theme-eid-adha .event-background,
        body.event-theme-islamic-new-year .event-background {
            background-image:
                linear-gradient(135deg, var(--event-background) 0%, #ffffff 72%),
                repeating-linear-gradient(60deg, color-mix(in srgb, var(--event-primary) 12%, transparent) 0 1px, transparent 1px 20px),
                repeating-linear-gradient(120deg, color-mix(in srgb, var(--event-accent) 10%, transparent) 0 1px, transparent 1px 24px);
        }

        body.event-theme-maulid .event-background,
        body.event-theme-isra-miraj .event-background {
            background-image:
                linear-gradient(135deg, var(--event-background) 0%, #ffffff 76%),
                repeating-linear-gradient(135deg, color-mix(in srgb, var(--event-primary) 13%, transparent) 0 1px, transparent 1px 22px);
        }

        .event-announcement {
            color: var(--event-secondary);
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--event-primary) 94%, black) 0%, var(--event-primary-dark) 100%);
            box-shadow: 0 18px 50px color-mix(in srgb, var(--event-primary) 22%, transparent);
        }

        .event-announcement::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.28;
            background-image:
                repeating-linear-gradient(45deg, transparent 0 18px, rgba(255, 255, 255, 0.22) 18px 19px);
            pointer-events: none;
        }

        .event-pill {
            background: color-mix(in srgb, var(--event-secondary) 18%, transparent);
            color: var(--event-secondary);
            border-color: color-mix(in srgb, var(--event-secondary) 36%, transparent);
        }

        .event-background-image {
            background-size: cover;
            background-position: center;
            opacity: 0.24;
            filter: saturate(0.95) contrast(0.92);
            pointer-events: none;
        }

        html.dark .event-background-image {
            opacity: 0.16;
        }
    </style>
</head>

<body class="font-sans text-slate-800 antialiased min-h-screen relative {{ $eventTheme ? 'has-event-theme event-theme-'.$eventTheme->style : '' }}">
    <!-- Main Background -->
    <div class="fixed inset-0 bg-main event-background z-[-3]"></div>
    <div class="fixed inset-0 bg-slate-100/60 event-background-overlay z-[-2]"></div>
    @if ($eventBackgroundImageUrl)
        <div class="fixed inset-0 event-background-image z-[-1]"
            style="background-image: url('{{ $eventBackgroundImageUrl }}')"></div>
    @endif
    @php
        $school = \App\Models\Setting::get('school_name', 'Alifia Modern School');
        $tagline = \App\Models\Setting::get('tagline', 'Modern School');
        $current = request()->route()->getName();
    @endphp

    {{-- Header --}}
    @php
        $links = [
            ['name' => __('Beranda'), 'route' => 'home'],
            ['name' => __('Tentang Kami'), 'route' => 'about'],
            ['name' => __('Program'), 'route' => 'programs.index'],
            ['name' => __('Berita'), 'route' => 'news.index'],
            ['name' => __('Guru'), 'route' => 'teachers.index'],
            ['name' => __('Kontak'), 'route' => 'contact'],
        ];
        // Alat internal: hanya muncul di unit yang menyalakannya.
        $moreLinks = array_values(
            array_filter([
                config('features.adiwiyata') ? ['name' => __('Adiwiyata'), 'route' => 'adiwiyata'] : null,
                config('features.ypdh_ai') ? ['name' => __('YPDH AI'), 'route' => 'ypdh-ai'] : null,
            ]),
        );
        $ppdbYear = \App\Models\Setting::get('ppdb_year', '2026');
    @endphp
    <header class="sticky top-4 z-40 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="animate-fade-up">
            {{-- Desktop nav starts at xl: below that the row (logo + 7 links + tools) cannot fit. --}}
            <div
                class="liquid-glass rounded-[1.75rem] py-2.5 pl-4 pr-2.5 sm:pl-5 flex items-center justify-between gap-4 apple-transition">
                <x-logo :name="$school" :tagline="$tagline" class="min-w-0" />

                <nav aria-label="{{ __('Navigasi') }}" class="hidden xl:flex items-center gap-1 text-sm font-medium">
                    @foreach ($links as $link)
                        @php $active = str_starts_with($current ?? '', explode('.', $link['route'])[0]) || $current === $link['route']; @endphp
                        <a href="{{ route($link['route']) }}" wire:navigate
                            @if ($active) aria-current="page" @endif
                            class="whitespace-nowrap rounded-full px-3.5 2xl:px-4 py-2.5 transition {{ $active ? 'bg-brand-50 text-brand-800 font-semibold' : 'text-slate-600 hover:bg-white hover:text-slate-900' }}">
                            {{ $link['name'] }}
                        </a>
                    @endforeach

                    @if ($moreLinks)
                        <div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false"
                            class="relative">
                            <button type="button" @click="open = !open" :aria-expanded="open ? 'true' : 'false'"
                                aria-haspopup="true"
                                class="flex items-center gap-1.5 whitespace-nowrap rounded-full px-3.5 2xl:px-4 py-2.5 text-slate-600 transition hover:bg-white hover:text-slate-900">
                                {{ __('Lainnya') }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.2" stroke="currentColor" class="h-3.5 w-3.5 transition-transform"
                                    :class="open && 'rotate-180'" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-cloak x-transition:enter="ease-ios duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="absolute right-0 mt-2 w-52 liquid-glass rounded-2xl p-2 shadow-lg z-50">
                                @foreach ($moreLinks as $link)
                                    <a href="{{ route($link['route']) }}" @click="open = false"
                                        class="block rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-white transition">{{ $link['name'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </nav>

                <div class="flex shrink-0 items-center gap-1.5">
                    <livewire:public.global-search />
                    <div class="hidden sm:block"><x-language-switcher /></div>
                    <x-dark-mode-toggle size="md" class="hidden sm:inline-flex" />
                    <a href="{{ route('ppdb.create') }}" wire:navigate
                        class="btn-brand hidden sm:inline-flex !gap-2 !px-5 !py-2.5 !text-sm whitespace-nowrap">
                        PPDB <span class="xl:hidden 2xl:inline">{{ $ppdbYear }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <button type="button" x-data @click="$dispatch('toggle-mobile-nav')" aria-label="{{ __('Menu') }}"
                        class="xl:hidden h-10 w-10 flex items-center justify-center rounded-full bg-white text-slate-700 hover:bg-slate-50 transition border border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div x-data="{ open: false }" @toggle-mobile-nav.window="open = !open" x-show="open" x-cloak
            class="xl:hidden mt-2 liquid-glass rounded-[1.5rem] p-3 shadow-lg"
            x-transition:enter="ease-ios duration-300" x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-ios duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4">
            <nav aria-label="{{ __('Navigasi') }}" class="space-y-1">
                @foreach ($links as $link)
                    @php $active = str_starts_with($current ?? '', explode('.', $link['route'])[0]) || $current === $link['route']; @endphp
                    <a href="{{ route($link['route']) }}" wire:navigate @click="open = false"
                        @if ($active) aria-current="page" @endif
                        class="block rounded-xl px-4 py-3 text-[15px] transition {{ $active ? 'bg-brand-50 font-semibold text-brand-800' : 'font-medium text-slate-700 hover:bg-white' }}">{{ $link['name'] }}</a>
                @endforeach
                @if ($moreLinks)
                    <div class="mt-3 pt-3 border-t border-slate-200">
                        <div class="px-4 pb-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
                            {{ __('Lainnya') }}</div>
                        @foreach ($moreLinks as $link)
                            <a href="{{ route($link['route']) }}" @click="open = false"
                                class="block rounded-xl px-4 py-3 text-[15px] font-medium text-slate-700 hover:bg-white transition">{{ $link['name'] }}</a>
                        @endforeach
                    </div>
                @endif
                <a href="{{ route('ppdb.create') }}" wire:navigate @click="open = false"
                    class="btn-brand mt-2 w-full sm:hidden">PPDB {{ $ppdbYear }}</a>
                <div class="mt-3 pt-3 border-t border-slate-200 flex items-center gap-2 sm:hidden">
                    <x-language-switcher :stacked="true" />
                    <x-dark-mode-toggle size="md" class="shrink-0" />
                </div>
            </nav>
        </div>
    </header>

    @if ($eventTheme)
        <section class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-8 animate-fade-up delay-100">
            <div class="event-announcement relative overflow-hidden rounded-[2rem] px-5 sm:px-6 py-4 border border-white/30">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div class="min-w-0">
                        <div class="text-xs font-bold uppercase tracking-wider opacity-80">{{ __('Tema Hari Besar') }}</div>
                        <div class="text-lg sm:text-xl font-extrabold leading-tight">{{ $eventTheme->name }}</div>
                        @if ($eventTheme->message)
                            <div class="text-sm font-medium opacity-90 mt-1">{{ $eventTheme->message }}</div>
                        @endif
                    </div>
                    <div class="event-pill inline-flex shrink-0 items-center justify-center rounded-full border px-4 py-2 text-xs font-bold">
                        {{ $eventTheme->repeat_annually ? $eventTheme->start_date->format('d/m') : $eventTheme->start_date->format('d/m/Y') }}
                        -
                        {{ $eventTheme->repeat_annually ? $eventTheme->end_date->format('d/m') : $eventTheme->end_date->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </section>
    @endif

    <main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="liquid-glass rounded-[2rem] p-6 sm:p-8 lg:p-10 shadow-sm border border-white/80 min-h-[60vh] mb-12">
            {{ $slot }}
        </div>
    </main>

    {{-- Footer --}}
    @php
        $socialIcons = [
            'instagram' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
            'facebook' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            'youtube' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
            'tiktok' =>
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        ];
        $socialLabels = [
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
        ];
        $contactItems = [
            [\App\Models\Setting::get('address'), 'M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z'],
            [\App\Models\Setting::get('phone'), 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.08-7.074-6.996l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z'],
            [\App\Models\Setting::get('email'), 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
        ];
        $stats = \App\Services\VisitorStats::summary();
    @endphp
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <footer class="liquid-glass relative z-20 rounded-[2rem] px-6 pt-14 pb-8 sm:px-10 lg:px-12 lg:pt-16">
            <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-12">
                <div class="space-y-6 sm:col-span-2 lg:col-span-5">
                    <x-logo :name="$school" :tagline="$tagline" />
                    <div class="prose prose-sm max-w-sm text-[14px] leading-relaxed text-slate-600">
                        {!! \App\Models\Setting::get('footer_about') !!}</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['instagram', 'facebook', 'youtube', 'tiktok'] as $sm)
                            @if ($url = \App\Models\Setting::get($sm))
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                    aria-label="{{ $socialLabels[$sm] }}"
                                    class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 transition hover:text-brand-700 hover:border-brand-200">
                                    {!! $socialIcons[$sm] !!}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <h2 class="mb-5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Navigasi') }}</h2>
                    <ul class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        @foreach ($links as $link)
                            <li><a href="{{ route($link['route']) }}" wire:navigate
                                    class="text-slate-700 transition hover:text-brand-700">{{ $link['name'] }}</a></li>
                        @endforeach
                        @foreach ($moreLinks as $link)
                            <li><a href="{{ route($link['route']) }}"
                                    class="text-slate-700 transition hover:text-brand-700">{{ $link['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="lg:col-span-4">
                    <h2 class="mb-5 text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ __('Kontak') }}</h2>
                    <ul class="space-y-3.5 text-sm text-slate-600">
                        @foreach ($contactItems as [$value, $icon])
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.6" stroke="currentColor" class="mt-0.5 h-[18px] w-[18px] shrink-0 text-brand-700"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                                </svg>
                                <span>{{ $value }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('login') }}" wire:navigate class="btn-outline mt-6 !px-4 !py-2.5 !text-[13px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                            stroke="currentColor" class="h-4 w-4" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        {{ __('Login Staff') }}
                    </a>
                </div>
            </div>

            {{-- Visitor stats --}}
            <div class="mt-12 flex flex-col gap-5 rounded-2xl bg-white/60 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap gap-x-8 gap-y-2 text-[13px] text-slate-600">
                    <p class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2" aria-hidden="true">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-brand-500"></span>
                        </span>
                        <span><b class="font-bold text-slate-900">{{ number_format($stats['online']) }}</b> {{ __('Sekarang aktif') }}</span>
                    </p>
                    <p><b class="font-bold text-slate-900">{{ number_format($stats['today']) }}</b> {{ __('Pengunjung') }} {{ Str::lower(__('Hari Ini')) }}</p>
                    <p><b class="font-bold text-slate-900">{{ number_format($stats['total_visitors']) }}</b> {{ __('Pengunjung unik') }}</p>
                    <p><b class="font-bold text-slate-900">{{ number_format($stats['total_views']) }}</b> {{ __('Total kunjungan halaman') }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="mr-1 text-xs text-slate-500">{{ __('Pengunjung dari') }}</span>
                    @forelse ($stats['top_countries'] as $c)
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs text-slate-700"
                            title="{{ $c['country'] }} — {{ number_format($c['total']) }} pengunjung">
                            <span class="leading-none">{{ \App\Services\GeoIp::flag($c['country_code']) }}</span>
                            <span class="font-semibold">{{ $c['country_code'] }}</span>
                            <span class="text-slate-500">{{ number_format($c['total']) }}</span>
                        </span>
                    @empty
                        <span class="text-xs italic text-slate-500">{{ __('Belum ada data pengunjung dari negara mana pun.') }}</span>
                    @endforelse
                </div>
            </div>

            <div
                class="mt-8 flex flex-col items-center justify-between gap-3 border-t border-slate-200 pt-6 text-xs text-slate-500 md:flex-row">
                <div>&copy; {{ date('Y') }} {{ $school }}. All rights reserved.</div>
                <div>Built with ❤️ by <a href="https://www.fahmiealkhudhorie.site" target="_blank" rel="noopener"
                        class="transition hover:text-brand-700">Fahmie Al Khudhorie</a></div>
            </div>
        </footer>
    </div>

    {{-- Scroll to Top Button --}}
    <div x-data="{ show: false }" x-on:scroll.window="show = window.scrollY > 400" x-cloak>
        <button x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-90"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-brand-700 text-white shadow-lg shadow-brand-700/30 flex items-center justify-center hover:bg-brand-800 apple-transition hover:scale-110 hover:-translate-y-1"
            aria-label="{{ __('Kembali ke atas') }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>
        </button>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
