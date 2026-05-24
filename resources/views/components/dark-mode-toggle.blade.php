@props(['size' => 'md'])

@php
    $sizeClass = match ($size) {
        'sm' => 'h-8 w-8',
        'lg' => 'h-12 w-12',
        default => 'h-10 w-10',
    };
    $iconClass = match ($size) {
        'sm' => 'h-4 w-4',
        'lg' => 'h-6 w-6',
        default => 'h-5 w-5',
    };
@endphp

<button type="button" x-data
    @click="
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
    "
    {{ $attributes->merge(['class' => "$sizeClass relative rounded-full flex items-center justify-center bg-white/70 dark:bg-slate-800/80 text-slate-700 dark:text-amber-300 border border-white/60 dark:border-slate-700 shadow-sm hover:scale-110 active:scale-95 transition-all duration-300 ease-out backdrop-blur-md"]) }}
    title="Toggle Dark Mode" aria-label="Toggle Dark Mode">
    {{-- Sun icon (visible in dark mode) --}}
    <svg class="{{ $iconClass }} hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
    </svg>
    {{-- Moon icon (visible in light mode) --}}
    <svg class="{{ $iconClass }} block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
    </svg>
</button>
