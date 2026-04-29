<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Panel' }} - {{ \App\Models\Setting::get('school_name') }}</title>
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-brand-styles />
</head>

<body class="font-sans bg-slate-100 min-h-screen">
    @php
        $user = auth()->user();
        $isAdmin = $user->isAdmin();
        $base = $isAdmin ? 'admin' : 'guru';
        $nav = $isAdmin
            ? [
                ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['route' => 'admin.programs', 'label' => 'Program', 'icon' => 'academic-cap'],
                ['route' => 'admin.news', 'label' => 'Berita', 'icon' => 'newspaper'],
                ['route' => 'admin.teachers', 'label' => 'Guru', 'icon' => 'users'],
                ['route' => 'admin.ppdb', 'label' => 'PPDB', 'icon' => 'document-text'],
                ['route' => 'admin.contacts', 'label' => 'Pesan', 'icon' => 'envelope'],
                ['route' => 'admin.visits', 'label' => 'Kunjungan', 'icon' => 'calendar'],
                ['route' => 'admin.users', 'label' => 'Pengguna', 'icon' => 'user-group'],
                ['route' => 'admin.icons', 'label' => 'Referensi Icon', 'icon' => 'sparkles'],
                ['route' => 'admin.settings', 'label' => 'Pengaturan', 'icon' => 'cog'],
            ]
            : [
                ['route' => 'guru.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                ['route' => 'guru.news', 'label' => 'Berita Saya', 'icon' => 'newspaper'],
                ['route' => 'guru.profile', 'label' => 'Profil', 'icon' => 'user'],
            ];
        $current = request()->route()->getName();
    @endphp

    <div x-data="{ sidebarOpen: false, profileOpen: false }" @keydown.escape.window="sidebarOpen = false; profileOpen = false">
        {{-- Mobile backdrop --}}
        <div
            x-show="sidebarOpen"
            x-transition:enter="ease-ios duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-ios duration-250"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            x-cloak
            class="lg:hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm"
        ></div>

        {{-- Sidebar (drawer di mobile, fixed di desktop) --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-200 flex flex-col
                   lg:w-64 lg:translate-x-0
                   transform transition-transform ease-ios duration-400 will-change-transform"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        >
            <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                <x-logo image-key="app_logo" :tagline="ucfirst($base)" :dark="true" />
                <button @click="sidebarOpen = false" class="lg:hidden h-9 w-9 rounded-lg hover:bg-slate-800 flex items-center justify-center text-slate-300">
                    <x-icon name="x-mark" class="h-5 w-5" />
                </button>
            </div>
            <nav class="p-3 space-y-1 overflow-y-auto flex-1">
                @foreach ($nav as $item)
                    @php $active = $current === $item['route']; @endphp
                    <a href="{{ route($item['route']) }}"
                        @click="sidebarOpen = false"
                        class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm transition-all ease-ios duration-200 active:scale-[0.97] {{ $active ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0 {{ $active ? 'text-white' : 'text-slate-400' }}" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="p-3 border-t border-slate-800">
                <a href="{{ route('home') }}" target="_blank"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 text-xs text-slate-400 hover:bg-slate-800 hover:text-white transition-colors ease-ios duration-200">
                    <x-icon name="globe" class="h-4 w-4" />
                    <span>Lihat Situs Publik</span>
                </a>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="lg:pl-64 min-h-screen flex flex-col">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
                <div class="flex items-center justify-between px-4 sm:px-6 h-16 gap-3">
                    <div class="flex items-center gap-2 min-w-0">
                        <button @click="sidebarOpen = true"
                            class="lg:hidden h-10 w-10 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-700 transition-all ease-ios duration-200 active:scale-90">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        </button>
                        <h1 class="font-bold text-slate-900 text-base sm:text-lg truncate">{{ $title ?? '' }}</h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('home') }}" target="_blank"
                            class="hidden md:inline-flex text-xs text-slate-500 hover:text-brand-600 transition-colors">Lihat Situs ↗</a>

                        {{-- Profile dropdown --}}
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                class="flex items-center gap-2 rounded-full p-1 pr-2 sm:pr-3 hover:bg-slate-100 transition-all ease-ios duration-200 active:scale-95">
                                <img src="{{ $user->photoUrl() }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white" alt="">
                                <div class="hidden sm:block text-left text-xs leading-tight">
                                    <div class="font-semibold text-slate-900 truncate max-w-[120px]">{{ $user->name }}</div>
                                    <div class="text-slate-500">{{ ucfirst($user->role) }}</div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="hidden sm:block h-4 w-4 text-slate-400 transition-transform ease-ios duration-200"
                                    :class="profileOpen && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-show="profileOpen" x-cloak
                                x-transition:enter="ease-ios duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="ease-ios duration-150"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl bg-white shadow-xl ring-1 ring-slate-100 p-2 z-50">
                                <div class="px-3 py-2 border-b border-slate-100">
                                    <div class="text-sm font-semibold text-slate-900 truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500 truncate">{{ $user->email }}</div>
                                </div>
                                @if (! $isAdmin)
                                    <a href="{{ route('guru.profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded-lg transition-colors">
                                        <x-icon name="user" class="h-4 w-4 text-slate-400" />
                                        Profil Saya
                                    </a>
                                @endif
                                <a href="{{ route('home') }}" target="_blank" class="md:hidden flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50 rounded-lg transition-colors">
                                    <x-icon name="globe" class="h-4 w-4 text-slate-400" />
                                    Lihat Situs
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block">@csrf
                                    <button class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors text-left">
                                        <x-icon name="x-mark" class="h-4 w-4" />
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 flex-1">
                <div x-data x-init="$el.classList.add('animate-page-in')" class="opacity-0">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
    <x-toaster />
    @livewireScripts
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'success',
                    message: @json(session('success'))
                }
            })));
        </script>
    @endif
</body>

</html>
