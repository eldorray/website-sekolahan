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
    <title>{{ $title ?? \App\Models\Setting::get('school_name', 'Alifia Modern School') }}</title>
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-brand-styles />
    @stack('styles')
    <style>
        .liquid-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
        }

        .liquid-glass-dark {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }

        .apple-transition {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .bg-main {
            background-image: url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&q=80&w=2000');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
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
            animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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
    </style>
</head>

<body class="font-sans text-slate-800 antialiased min-h-screen relative">
    <!-- Main Background -->
    <div class="fixed inset-0 bg-main z-[-1]"></div>
    <div class="fixed inset-0 bg-slate-100/60 backdrop-blur-xl z-[-1]"></div>
    @php
        $school = \App\Models\Setting::get('school_name', 'Alifia Modern School');
        $tagline = \App\Models\Setting::get('tagline', 'Modern School');
        $current = request()->route()->getName();
    @endphp

    {{-- Header --}}
    <header class="sticky top-4 z-40 max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="animate-fade-up">
            <div
                class="liquid-glass rounded-[2rem] px-4 md:px-6 py-3 flex items-center justify-between relative apple-transition hover:shadow-lg">
                <x-logo :name="$school" :tagline="$tagline" />

                <nav
                    class="hidden lg:flex items-center gap-2 text-sm font-medium text-slate-600 bg-white/50 rounded-full p-1.5 border border-white/60">
                    @php
                        $links = [
                            ['name' => 'Beranda', 'route' => 'home'],
                            ['name' => 'Tentang Kami', 'route' => 'about'],
                            ['name' => 'Program', 'route' => 'programs.index'],
                            ['name' => 'Berita', 'route' => 'news.index'],
                            ['name' => 'Guru', 'route' => 'teachers.index'],
                            ['name' => 'Kontak', 'route' => 'contact'],
                        ];
                    @endphp
                    @foreach ($links as $link)
                        @php $active = str_starts_with($current ?? '', explode('.', $link['route'])[0]) || $current === $link['route']; @endphp
                        <a href="{{ route($link['route']) }}" wire:navigate
                            class="px-5 py-2 rounded-full transition {{ $active ? 'text-slate-900 bg-white shadow-sm' : 'hover:bg-white/60' }}">
                            {{ $link['name'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-3">
                    <x-dark-mode-toggle size="md" class="hidden sm:inline-flex" />
                    <a href="{{ route('ppdb.create') }}" wire:navigate
                        class="hidden sm:inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold apple-transition hover:scale-105 hover:shadow-lg shadow-brand-500/30">
                        PPDB {{ \App\Models\Setting::get('ppdb_year', '2026') }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <button x-data @click="$dispatch('toggle-mobile-nav')"
                        class="lg:hidden w-10 h-10 flex items-center justify-center rounded-full bg-white/80 text-slate-600 hover:bg-white transition border border-white/60 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div x-data="{ open: false }" @toggle-mobile-nav.window="open = !open" x-show="open" x-cloak
            class="lg:hidden mt-2 liquid-glass rounded-[1.5rem] p-4 shadow-lg border border-white/80"
            x-transition:enter="ease-ios duration-300" x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-ios duration-200"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4">
            <div class="space-y-1">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}" wire:navigate @click="open = false"
                        class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-white/60 transition">{{ $link['name'] }}</a>
                @endforeach
                <a href="{{ route('ppdb.create') }}" wire:navigate @click="open = false"
                    class="block mt-2 rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white text-center shadow-md">PPDB
                    {{ \App\Models\Setting::get('ppdb_year', '2026') }}</a>
            </div>
        </div>
    </header>

    <main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 animate-fade-up delay-100">
        <div class="liquid-glass rounded-[2rem] p-6 sm:p-8 lg:p-10 shadow-sm border border-white/80 min-h-[60vh] mb-12">
            {{ $slot }}
        </div>
    </main>

    {{-- Footer --}}
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-8 animate-fade-up delay-200">
        <footer class="liquid-glass rounded-[3rem] pt-16 pb-8 relative z-20">
            <div class="px-8 md:px-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
                    <div class="lg:col-span-4">
                        <x-logo :name="$school" :tagline="$tagline" />
                        <div
                            class="mt-6 text-[13px] text-slate-600 font-medium leading-relaxed prose prose-sm max-w-sm">
                            {!! \App\Models\Setting::get('footer_about') !!}</div>
                    </div>
                    <div class="lg:col-span-2">
                        <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Navigasi</h4>
                        <ul class="space-y-3 text-[13px] text-slate-600 font-medium">
                            @foreach ($links as $link)
                                <li><a href="{{ route($link['route']) }}" wire:navigate
                                        class="hover:text-brand-600 transition">{{ $link['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="lg:col-span-3">
                        <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Kontak</h4>
                        <ul class="space-y-3 text-[13px] text-slate-600 font-medium">
                            <li class="flex items-start gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-4 h-4 mt-0.5 text-slate-400 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg> <span>{{ \App\Models\Setting::get('address') }}</span></li>
                            <li class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-4 h-4 text-slate-400 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.08-7.074-6.996l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg> <span>{{ \App\Models\Setting::get('phone') }}</span></li>
                            <li class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                    class="w-4 h-4 text-slate-400 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg> <span>{{ \App\Models\Setting::get('email') }}</span></li>
                        </ul>
                    </div>
                    <div class="lg:col-span-3">
                        <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Ikuti Kami</h4>
                        @php
                            $socialIcons = [
                                'instagram' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
                                'facebook' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                                'youtube' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                                'tiktok' =>
                                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
                            ];
                            $socialLabels = [
                                'instagram' => 'Instagram',
                                'facebook' => 'Facebook',
                                'youtube' => 'YouTube',
                                'tiktok' => 'TikTok',
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-3 mb-6">
                            @foreach (['instagram', 'facebook', 'youtube', 'tiktok'] as $sm)
                                @if ($url = \App\Models\Setting::get($sm))
                                    <a href="{{ $url }}" target="_blank" rel="noopener"
                                        aria-label="{{ $socialLabels[$sm] }}"
                                        class="w-10 h-10 rounded-full bg-white/80 border border-white/60 flex items-center justify-center text-slate-600 hover:text-brand-600 hover:bg-white transition shadow-sm apple-transition hover:-translate-y-1">
                                        {!! $socialIcons[$sm] !!}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <div class="text-[13px] font-medium text-slate-500">
                            <a href="{{ route('login') }}" wire:navigate
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white/50 border border-white/60 hover:bg-white hover:text-brand-600 transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                Login Staff
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Visitor Stats Bar --}}
                @php $stats = \App\Services\VisitorStats::summary(); @endphp
                <div class="my-8 liquid-glass rounded-[1.5rem] p-5 sm:p-6 border border-white/80 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                        {{-- Stats numbers --}}
                        <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="text-center sm:text-left">
                                <div
                                    class="flex items-center gap-2 justify-center sm:justify-start text-emerald-600 mb-1">
                                    <span class="relative flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Online</span>
                                </div>
                                <div class="text-2xl font-extrabold text-slate-900">
                                    {{ number_format($stats['online']) }}</div>
                                <div class="text-[11px] font-medium text-slate-500">Sekarang aktif</div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div
                                    class="flex items-center gap-2 justify-center sm:justify-start text-brand-600 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Hari Ini</span>
                                </div>
                                <div class="text-2xl font-extrabold text-slate-900">
                                    {{ number_format($stats['today']) }}</div>
                                <div class="text-[11px] font-medium text-slate-500">Pengunjung</div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div
                                    class="flex items-center gap-2 justify-center sm:justify-start text-amber-500 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Total</span>
                                </div>
                                <div class="text-2xl font-extrabold text-slate-900">
                                    {{ number_format($stats['total_visitors']) }}</div>
                                <div class="text-[11px] font-medium text-slate-500">Pengunjung unik</div>
                            </div>
                            <div class="text-center sm:text-left">
                                <div
                                    class="flex items-center gap-2 justify-center sm:justify-start text-purple-600 mb-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">Page Views</span>
                                </div>
                                <div class="text-2xl font-extrabold text-slate-900">
                                    {{ number_format($stats['total_views']) }}</div>
                                <div class="text-[11px] font-medium text-slate-500">Total kunjungan halaman</div>
                            </div>
                        </div>

                        {{-- Top countries --}}
                        <div class="lg:col-span-5">
                            <div
                                class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-3 text-center lg:text-left">
                                Pengunjung dari</div>
                            @if (count($stats['top_countries']))
                                <div class="flex flex-wrap gap-2 justify-center lg:justify-start">
                                    @foreach ($stats['top_countries'] as $c)
                                        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-full bg-white/80 border border-white/60 shadow-sm text-[12px] font-medium text-slate-700"
                                            title="{{ $c['country'] }} — {{ number_format($c['total']) }} pengunjung">
                                            <span
                                                class="text-base leading-none">{{ \App\Services\GeoIp::flag($c['country_code']) }}</span>
                                            <span class="font-semibold">{{ $c['country_code'] }}</span>
                                            <span class="text-slate-400">·</span>
                                            <span
                                                class="font-bold text-slate-900">{{ number_format($c['total']) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-xs text-slate-400 italic text-center lg:text-left">Belum ada data
                                    pengunjung dari negara mana pun.</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div
                    class="pt-6 border-t border-white/60 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] font-medium text-slate-500 relative">
                    <div>&copy; {{ date('Y') }} {{ $school }}. All rights reserved.</div>
                    <div>Built with ❤️ by <a href="https://www.fahmiealkhudhorie.site" target="_blank"
                            class="hover:text-brand-600 transition">Fahmie Al Khudhorie</a></div>
                </div>
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
            class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-brand-600 text-white shadow-lg shadow-brand-500/30 flex items-center justify-center hover:bg-brand-700 apple-transition hover:scale-110 hover:-translate-y-1"
            aria-label="Kembali ke atas">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>
        </button>
    </div>

    @livewireScripts
    @stack('scripts')
</body>

</html>
