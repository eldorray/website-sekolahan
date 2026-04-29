<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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
<body class="font-sans bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <x-logo image-key="app_logo" class="justify-center" />
        </div>
        {{ $slot }}
        <p class="text-center text-xs text-slate-500 mt-6"><a href="{{ route('home') }}" class="hover:text-brand-600">← Kembali ke Beranda</a></p>
    </div>
    @livewireScripts
</body>
</html>
