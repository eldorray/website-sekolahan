<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Halaman terkunci: jangan diindeks mesin pencari. --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $heading }}</title>
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif&family=Plus+Jakarta+Sans:wght@400..700&display=swap">
    {{-- Hanya variabel warna brand (inline). Gaya di bawah sengaja tidak
         memakai berkas CSS hasil build: formulir PIN harus tetap layak walau
         berkas itu gagal termuat. --}}
    <x-brand-styles />
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
            background: #f3f5f0;
            color: #15211a;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .wrap {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .card {
            width: 100%;
            padding: 40px 32px 32px;
            border-radius: 32px;
            background: #fff;
            border: 1px solid #e2e6dd;
            box-shadow: 0 1px 2px rgb(21 33 26 / .04), 0 24px 60px -36px rgb(21 33 26 / .3);
            text-align: center;
        }

        .mark {
            width: 56px;
            height: 56px;
            margin: 0 auto 20px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--color-brand-50, #f3faf0);
            color: var(--color-brand-700, #3d6c22);
            box-shadow: inset 0 0 0 1px var(--color-brand-100, #e3f4d8);
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--color-brand-700, #3d6c22);
        }

        h1 {
            margin-top: 6px;
            font-family: 'Instrument Serif', Georgia, serif;
            font-weight: 400;
            font-size: 44px;
            line-height: 1.05;
            letter-spacing: -.01em;
        }

        .sub {
            margin-top: 10px;
            font-size: 14px;
            color: #4a554e;
        }

        form {
            margin-top: 28px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .pin {
            width: 100%;
            height: 56px;
            padding: 0 18px;
            border-radius: 16px;
            border: 1px solid #e2e6dd;
            background: #fafbf8;
            color: #15211a;
            font-family: inherit;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .5em;
            text-align: center;
            transition: border-color .15s, box-shadow .15s;
        }

        .pin::placeholder {
            letter-spacing: normal;
            font-size: 14px;
            font-weight: 500;
            color: #6b7570;
        }

        .pin:focus {
            outline: none;
            border-color: var(--color-brand-500, #65ad36);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-brand-500, #65ad36) 22%, transparent);
        }

        .pin[aria-invalid="true"] {
            border-color: #dc2626;
        }

        button {
            height: 52px;
            border: none;
            border-radius: 999px;
            background: var(--color-brand-700, #3d6c22);
            color: #fff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 24px -14px var(--color-brand-700, #3d6c22);
            transition: background .15s, transform .15s;
        }

        button:hover {
            background: var(--color-brand-800, #325620);
        }

        button:active {
            transform: scale(.98);
        }

        button:focus-visible {
            outline: 2px solid var(--color-brand-500, #65ad36);
            outline-offset: 3px;
        }

        .err {
            font-size: 13px;
            font-weight: 600;
            color: #b91c1c;
        }

        .note {
            margin-top: 24px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #f5f6f1;
            font-size: 13px;
            color: #4a554e;
        }

        .back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            color: #4a554e;
            text-decoration: none;
            transition: color .15s, background .15s;
        }

        .back:hover {
            color: #15211a;
            background: rgb(255 255 255 / .7);
        }

        @media (max-width: 420px) {
            .card {
                padding: 32px 22px 24px;
            }
        }

        @media(prefers-reduced-motion:reduce) {
            * {
                transition: none !important
            }
        }
    </style>
</head>

<body>
    <main class="wrap">
        <div class="card">
            <div class="mark">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <p class="eyebrow">{{ \App\Models\Setting::get('school_name', config('app.name')) }}</p>
            <h1>{{ $heading }}</h1>

            @if ($pinConfigured)
                <p class="sub">{{ __('Masukkan PIN dari admin untuk membuka halaman ini.') }}</p>

                <form method="POST" action="{{ $action }}">
                    @csrf
                    <input type="password" name="pin" class="pin" placeholder="{{ __('PIN') }}"
                        aria-label="{{ __('PIN') }}" inputmode="numeric" autocomplete="off" autofocus required
                        @error('pin') aria-invalid="true" aria-describedby="pin-err" @enderror>
                    <button type="submit">{{ __('Masuk') }}</button>
                    @error('pin')
                        <div class="err" id="pin-err" role="alert">{{ $message }}</div>
                    @enderror
                </form>
            @else
                <p class="sub">{{ __('Halaman ini terkunci.') }}</p>
                <div class="note">{{ __('Belum ada PIN yang diatur. Hubungi admin sekolah.') }}</div>
            @endif
        </div>

        <a class="back" href="{{ route('home') }}">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            {{ __('Kembali ke beranda') }}
        </a>
    </main>
</body>

</html>
