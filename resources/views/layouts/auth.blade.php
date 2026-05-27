<!DOCTYPE html>
<html lang="id" class="">

<head>
    <meta charset="UTF-8">
    <script>
        (function() {
            try {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ \App\Models\Setting::get('school_name', 'Sekolah') }}</title>
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <x-brand-styles />
</head>

<body class="font-sans bg-slate-50 dark:bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="absolute top-4 right-4">
            <x-dark-mode-toggle size="sm" />
        </div>
        <div class="text-center mb-6">
            <x-logo image-key="app_logo" class="justify-center" />
        </div>
        {{ $slot }}
        <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-6"><a href="{{ route('home') }}"
                wire:navigate class="hover:text-brand-600">← Kembali ke Beranda</a></p>
    </div>
    @livewireScripts
</body>

</html>
