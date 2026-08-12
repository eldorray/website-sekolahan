<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Halaman terkunci: jangan diindeks mesin pencari. --}}
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Monitoring Adiwiyata') }}</title>
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
    @endif
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
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: radial-gradient(circle at 50% 0%, #14532d 0%, #0b1a12 55%, #050b08 100%);
            color: #e2e8f0;
            line-height: 1.5;
        }

        .box {
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        .logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 18px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            background: linear-gradient(135deg, #16a34a, #166534);
            box-shadow: 0 10px 30px rgba(22, 163, 74, .35);
        }

        h1 {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: -.3px;
            color: #fff;
        }

        .sub {
            margin-top: 8px;
            font-size: 13px;
            color: #94a3b8;
        }

        form {
            margin-top: 26px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pin {
            width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, .16);
            background: rgba(255, 255, 255, .06);
            color: #fff;
            font-family: inherit;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 8px;
            text-align: center;
        }

        .pin::placeholder {
            letter-spacing: normal;
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
        }

        .pin:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, .22);
        }

        button {
            padding: 13px 18px;
            border: none;
            border-radius: 14px;
            background: #16a34a;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
        }

        button:hover {
            background: #15803d;
        }

        button:focus-visible {
            outline: 2px solid #22c55e;
            outline-offset: 2px;
        }

        .err {
            margin-top: 4px;
            font-size: 13px;
            font-weight: 600;
            color: #fca5a5;
        }

        .note {
            margin-top: 26px;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .04);
            font-size: 13px;
            color: #cbd5e1;
        }

        .back {
            display: inline-block;
            margin-top: 26px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
        }

        .back:hover {
            color: #94a3b8;
        }

        @media(prefers-reduced-motion:reduce) {
            * {
                transition: none !important
            }
        }
    </style>
</head>

<body>
    <div class="box">
        <div class="logo">🌿</div>
        <h1>{{ __('Monitoring Adiwiyata') }}</h1>

        @if ($pinConfigured)
            <p class="sub">{{ __('Masukkan PIN dari admin untuk membuka halaman ini.') }}</p>

            <form method="POST" action="{{ route('adiwiyata.unlock') }}">
                @csrf
                <input type="password" name="pin" class="pin" placeholder="{{ __('PIN') }}"
                    aria-label="{{ __('PIN') }}" inputmode="numeric" autocomplete="off" autofocus required>
                <button type="submit">{{ __('Masuk') }}</button>
                @error('pin')
                    <div class="err">{{ $message }}</div>
                @enderror
            </form>
        @else
            <p class="sub">{{ __('Halaman ini terkunci.') }}</p>
            <div class="note">{{ __('Belum ada PIN yang diatur. Hubungi admin sekolah.') }}</div>
        @endif

        <a class="back" href="{{ route('home') }}">← {{ __('Kembali ke beranda') }}</a>
    </div>
</body>

</html>
