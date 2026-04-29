<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
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
</head>

<body class="font-sans bg-white text-slate-800 antialiased">
    @php
        $school = \App\Models\Setting::get('school_name', 'Alifia Modern School');
        $tagline = \App\Models\Setting::get('tagline', 'Modern School');
        $current = request()->route()->getName();
    @endphp

    {{-- Header --}}
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b border-slate-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between">
                <x-logo :name="$school" :tagline="$tagline" />

                <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-slate-600">
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
                        <a href="{{ route($link['route']) }}"
                            class="relative py-1 transition hover:text-brand-600 {{ $active ? 'text-brand-600' : '' }}">
                            {{ $link['name'] }}
                            @if ($active)
                                <span class="absolute -bottom-1 left-0 right-0 h-0.5 bg-brand-500 rounded-full"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('ppdb.create') }}" class="hidden sm:inline-flex btn-primary">
                        PPDB {{ \App\Models\Setting::get('ppdb_year', '2026') }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <button x-data @click="$dispatch('toggle-mobile-nav')"
                        class="lg:hidden rounded-lg p-2 text-slate-600 hover:bg-slate-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div x-data="{ open: false }" @toggle-mobile-nav.window="open = !open" x-show="open" x-cloak
            class="lg:hidden border-t border-slate-100">
            <div class="px-4 py-3 space-y-1">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                        class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-brand-50">{{ $link['name'] }}</a>
                @endforeach
                <a href="{{ route('ppdb.create') }}"
                    class="block rounded-lg bg-brand-500 px-3 py-2 text-sm font-semibold text-white">PPDB
                    {{ \App\Models\Setting::get('ppdb_year', '2026') }}</a>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-900 text-slate-300 mt-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <x-logo :name="$school" :tagline="$tagline" :dark="true" />
                    <div class="mt-4 text-sm text-slate-400 leading-relaxed prose prose-sm prose-invert max-w-none">
                        {!! \App\Models\Setting::get('footer_about') !!}</div>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Navigasi</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        @foreach ($links as $link)
                            <li><a href="{{ route($link['route']) }}"
                                    class="hover:text-brand-300">{{ $link['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li>{{ \App\Models\Setting::get('address') }}</li>
                        <li>{{ \App\Models\Setting::get('phone') }}</li>
                        <li>{{ \App\Models\Setting::get('email') }}</li>
                        <li>{{ \App\Models\Setting::get('website') }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Ikuti Kami</h4>
                    <div class="flex gap-3">
                        @foreach (['instagram', 'facebook', 'youtube', 'tiktok'] as $sm)
                            @if ($url = \App\Models\Setting::get($sm))
                                <a href="{{ $url }}" target="_blank"
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800 hover:bg-brand-500 transition">
                                    <span class="text-xs uppercase">{{ substr($sm, 0, 2) }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-6 text-xs text-slate-500">
                        <a href="{{ route('login') }}" class="hover:text-brand-300">Login Staff</a>
                    </div>
                </div>
            </div>
            <div
                class="mt-10 border-t border-slate-800 pt-6 text-xs text-slate-500 flex flex-col sm:flex-row justify-between gap-2">
                <div>&copy; {{ date('Y') }} {{ $school }}. All rights reserved.</div>
                <div>Built with ❤️ by <a href="https://www.fahmiealkhudhorie.site" target="_blank"
                        class="hover:text-brand-300">Fahmie Al Khudhorie</a></div>
            </div>
        </div>
    </footer>

    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
