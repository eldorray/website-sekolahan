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
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            opacity: 0;
            animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
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
    <header class="sticky top-4 z-40 mx-4 sm:mx-6 lg:mx-8 mb-8">
        <div class="max-w-[1400px] mx-auto animate-fade-up">
            <div class="liquid-glass rounded-[2rem] px-4 md:px-6 py-3 flex items-center justify-between relative apple-transition hover:shadow-lg">
                <x-logo :name="$school" :tagline="$tagline" />

                <nav class="hidden lg:flex items-center gap-2 text-sm font-medium text-slate-600 bg-white/50 rounded-full p-1.5 border border-white/60">
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
                            class="px-5 py-2 rounded-full transition {{ $active ? 'text-slate-900 bg-white shadow-sm' : 'hover:bg-white/60' }}">
                            {{ $link['name'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('ppdb.create') }}" class="hidden sm:inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-brand-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold apple-transition hover:scale-105 hover:shadow-lg shadow-brand-500/30">
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
            class="lg:hidden mt-2 max-w-[1400px] mx-auto liquid-glass rounded-[1.5rem] p-4 shadow-lg border border-white/80"
            x-transition:enter="ease-ios duration-300"
            x-transition:enter-start="opacity-0 -translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-ios duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            >
            <div class="space-y-1">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                        class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-white/60 transition">{{ $link['name'] }}</a>
                @endforeach
                <a href="{{ route('ppdb.create') }}"
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
                        <div class="mt-6 text-[13px] text-slate-600 font-medium leading-relaxed prose prose-sm max-w-sm">
                            {!! \App\Models\Setting::get('footer_about') !!}</div>
                    </div>
                    <div class="lg:col-span-2">
                        <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Navigasi</h4>
                        <ul class="space-y-3 text-[13px] text-slate-600 font-medium">
                            @foreach ($links as $link)
                                <li><a href="{{ route($link['route']) }}"
                                        class="hover:text-brand-600 transition">{{ $link['name'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="lg:col-span-3">
                        <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Kontak</h4>
                        <ul class="space-y-3 text-[13px] text-slate-600 font-medium">
                            <li class="flex items-start gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mt-0.5 text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg> <span>{{ \App\Models\Setting::get('address') }}</span></li>
                            <li class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.08-7.074-6.996l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg> <span>{{ \App\Models\Setting::get('phone') }}</span></li>
                            <li class="flex items-center gap-2"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-400 shrink-0"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg> <span>{{ \App\Models\Setting::get('email') }}</span></li>
                        </ul>
                    </div>
                    <div class="lg:col-span-3">
                        <h4 class="font-bold text-slate-900 mb-6 text-[14px]">Ikuti Kami</h4>
                        <div class="flex flex-wrap gap-3 mb-6">
                            @foreach (['instagram', 'facebook', 'youtube', 'tiktok'] as $sm)
                                @if ($url = \App\Models\Setting::get($sm))
                                    <a href="{{ $url }}" target="_blank"
                                        class="w-10 h-10 rounded-full bg-white/80 border border-white/60 flex items-center justify-center text-slate-600 hover:text-brand-600 hover:bg-white transition shadow-sm apple-transition hover:-translate-y-1">
                                        <span class="text-[10px] font-bold uppercase">{{ substr($sm, 0, 2) }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <div class="text-[13px] font-medium text-slate-500">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white/50 border border-white/60 hover:bg-white hover:text-brand-600 transition shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                Login Staff
                            </a>
                        </div>
                    </div>
                </div>
                <div class="pt-6 border-t border-white/60 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] font-medium text-slate-500 relative">
                    <div>&copy; {{ date('Y') }} {{ $school }}. All rights reserved.</div>
                    <div>Built with ❤️ by <a href="https://www.fahmiealkhudhorie.site" target="_blank"
                            class="hover:text-brand-600 transition">Fahmie Al Khudhorie</a></div>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
    <script src="//unpkg.com/alpinejs" defer></script>
</body>

</html>
