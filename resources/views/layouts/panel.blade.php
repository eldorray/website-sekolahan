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
    </style>
</head>

<body class="font-sans text-slate-800 min-h-screen relative">
    <!-- Main Background -->
    <div class="fixed inset-0 bg-main z-[-1]"></div>
    <div class="fixed inset-0 bg-slate-100/60 backdrop-blur-xl z-[-1]"></div>
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

    <div x-data="{ sidebarOpen: true, mobileSidebarOpen: false, profileOpen: false }" @keydown.escape.window="mobileSidebarOpen = false; profileOpen = false" class="flex min-h-screen">
        {{-- Mobile backdrop --}}
        <div
            x-show="mobileSidebarOpen"
            x-transition:enter="ease-ios duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-ios duration-250"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileSidebarOpen = false"
            x-cloak
            class="lg:hidden fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-md"
        ></div>

        {{-- Sidebar (drawer di mobile, collapsible di desktop) --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col liquid-glass-dark text-slate-200
                   transform transition-all ease-ios duration-400 will-change-transform lg:relative"
            :class="[
                mobileSidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full lg:translate-x-0 lg:w-64 lg:m-4 lg:rounded-[2rem]',
                !sidebarOpen && 'lg:w-20'
            ]"
        >
            <div class="p-5 border-b border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden transition-all duration-300" :class="!sidebarOpen && 'lg:opacity-0 lg:w-0'">
                    <x-logo image-key="app_logo" :tagline="ucfirst($base)" :dark="true" />
                </div>
                <div class="hidden lg:flex" :class="!sidebarOpen && 'w-full justify-center'">
                    <button @click="sidebarOpen = !sidebarOpen" class="h-9 w-9 rounded-xl hover:bg-white/10 flex items-center justify-center text-slate-300 apple-transition">
                        <x-icon name="bars-3" class="h-5 w-5" />
                    </button>
                </div>
                <button @click="mobileSidebarOpen = false" class="lg:hidden h-9 w-9 rounded-xl hover:bg-white/10 flex items-center justify-center text-slate-300 apple-transition">
                    <x-icon name="x-mark" class="h-5 w-5" />
                </button>
            </div>
            <nav class="p-3 space-y-1 overflow-y-auto flex-1 no-scrollbar">
                @foreach ($nav as $item)
                    @php $active = $current === $item['route']; @endphp
                    <a href="{{ route($item['route']) }}"
                        @click="mobileSidebarOpen = false"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm apple-transition {{ $active ? 'bg-white/20 text-white shadow-sm border border-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white border border-transparent' }}"
                        :title="!sidebarOpen ? '{{ $item['label'] }}' : ''"
                        >
                        <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0 {{ $active ? 'text-white' : 'text-slate-400' }}" />
                        <span class="whitespace-nowrap transition-all duration-300" :class="!sidebarOpen && 'lg:opacity-0 lg:w-0 lg:hidden'">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="p-3 border-t border-white/10">
                <a href="{{ route('home') }}" target="_blank"
                    class="flex items-center gap-3 rounded-xl px-3 py-2 text-xs text-slate-400 hover:bg-white/10 hover:text-white apple-transition"
                    :title="!sidebarOpen ? 'Lihat Situs Publik' : ''"
                    >
                    <x-icon name="globe" class="h-4 w-4 shrink-0" />
                    <span class="whitespace-nowrap transition-all duration-300" :class="!sidebarOpen && 'lg:opacity-0 lg:w-0 lg:hidden'">Lihat Situs Publik</span>
                </a>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-400 ease-ios" :class="sidebarOpen ? '' : ''">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 lg:mt-4 mx-4 lg:mx-8 mb-4">
                <div class="liquid-glass rounded-[2rem] flex items-center justify-between px-4 sm:px-6 h-16 gap-3 apple-transition hover:shadow-md">
                    <div class="flex items-center gap-2 min-w-0">
                        <button @click="mobileSidebarOpen = true"
                            class="lg:hidden h-10 w-10 rounded-xl hover:bg-white/50 flex items-center justify-center text-slate-700 apple-transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                        </button>
                        <h1 class="font-bold text-slate-900 text-base sm:text-lg truncate drop-shadow-sm">{{ $title ?? '' }}</h1>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('home') }}" target="_blank"
                            class="hidden md:inline-flex px-3 py-1.5 rounded-full bg-white/50 text-xs font-semibold text-slate-600 hover:text-brand-600 hover:bg-white apple-transition border border-white/60">Lihat Situs ↗</a>

                        {{-- Profile dropdown --}}
                        <div class="relative">
                            <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                class="flex items-center gap-2 rounded-full p-1 pr-2 sm:pr-3 bg-white/50 hover:bg-white apple-transition border border-white/60">
                                <img src="{{ $user->photoUrl() }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-white/80 shadow-sm" alt="">
                                <div class="hidden sm:block text-left text-xs leading-tight">
                                    <div class="font-semibold text-slate-900 truncate max-w-[120px] drop-shadow-sm">{{ $user->name }}</div>
                                    <div class="text-slate-500 font-medium">{{ ucfirst($user->role) }}</div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="hidden sm:block h-4 w-4 text-slate-500 transition-transform ease-ios duration-200"
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
                                class="absolute right-0 mt-3 w-56 origin-top-right rounded-2xl liquid-glass shadow-xl p-2 z-50 border border-white/80">
                                <div class="px-3 py-2 border-b border-white/60 mb-1">
                                    <div class="text-sm font-semibold text-slate-900 truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-slate-500 truncate font-medium">{{ $user->email }}</div>
                                </div>
                                @if (! $isAdmin)
                                    <a href="{{ route('guru.profile') }}" class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-white/80 rounded-xl apple-transition">
                                        <x-icon name="user" class="h-4 w-4 text-slate-500" />
                                        Profil Saya
                                    </a>
                                @endif
                                <a href="{{ route('home') }}" target="_blank" class="md:hidden flex items-center gap-2 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-white/80 rounded-xl apple-transition">
                                    <x-icon name="globe" class="h-4 w-4 text-slate-500" />
                                    Lihat Situs
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="block mt-1">@csrf
                                    <button class="w-full flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50/80 rounded-xl apple-transition text-left">
                                        <x-icon name="x-mark" class="h-4 w-4" />
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="px-4 sm:px-6 lg:px-8 pb-8 flex-1">
                <div x-data x-init="$el.classList.add('animate-page-in')" class="opacity-0">
                    <div class="liquid-glass rounded-[2rem] p-6 shadow-sm border border-white/80 min-h-[70vh]">
                        {{ $slot }}
                    </div>
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
