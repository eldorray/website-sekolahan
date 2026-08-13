<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('YPDH AI') }} — {{ __('Asisten AI untuk Guru') }}</title>
    @if ($favicon = \App\Models\Setting::imageUrl('favicon'))
        <link rel="icon" href="{{ $favicon }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    {{-- Pasang keadaan rail sebelum render supaya sidebar tidak berkedip lebar
         lalu menciut saat halaman dibuka. --}}
    <script>
        try {
            if (localStorage.getItem('ypdh_rail') === '1') document.documentElement.classList.add('rail');
        } catch (e) {}
    </script>
    <style>
        /* Sidebar menciut jadi rail ikon — hanya di layar lebar; di layar kecil
           sidebar tetap laci penuh. */
        /* Keadaan aktif ditulis di sini, bukan dirakit dari daftar kelas di JS,
           supaya ikon dan teksnya berubah bersama. */
        .tab.on {
            background: #fff;
            color: rgb(15 23 42);
            box-shadow: 0 1px 2px rgb(15 23 42 / .06);
        }

        .tab.on svg {
            color: rgb(37 99 235)
        }

        @media (min-width: 1024px) {
            html.rail #sidebar {
                width: 4rem;
                padding: .5rem
            }

            html.rail #sidebar .lbl {
                display: none
            }

            html.rail #sidebar .rail-row {
                justify-content: center;
                padding-left: 0;
                padding-right: 0
            }

            html.rail #sidebar .rail-hide {
                display: none
            }

            html.rail #sidebar .rail-stack {
                flex-direction: column
            }

            html.rail #sidebar .rail-only {
                display: flex
            }
        }

        [x-cloak],
        .hide {
            display: none !important
        }

        .scroll-thin::-webkit-scrollbar {
            width: 6px
        }

        .scroll-thin::-webkit-scrollbar-thumb {
            background: rgb(148 163 184 / .45);
            border-radius: 99px
        }

        .glass {
            background: rgb(255 255 255 / .72);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
        }

        /* Jawaban AI dirender dari markdown; beri jarak baca yang wajar. */
        .prosa {
            font-size: 15px;
            line-height: 1.75
        }

        .prosa p {
            margin: 0 0 .9rem
        }

        .prosa p:last-child {
            margin-bottom: 0
        }

        .prosa h2,
        .prosa h3 {
            font-weight: 700;
            font-size: 1.05rem;
            margin: 1.2rem 0 .5rem
        }

        .prosa ul,
        .prosa ol {
            margin: 0 0 .9rem;
            padding-left: 1.3rem
        }

        .prosa ul {
            list-style: disc
        }

        .prosa ol {
            list-style: decimal
        }

        .prosa li {
            margin: .2rem 0
        }

        .prosa code {
            background: rgb(241 245 249);
            padding: .1rem .35rem;
            border-radius: .35rem;
            font-size: .85em
        }

        .prosa pre {
            background: #0f172a;
            color: #e2e8f0;
            padding: .9rem 1rem;
            border-radius: .9rem;
            overflow-x: auto;
            margin: 0 0 .9rem
        }

        .prosa pre code {
            background: none;
            color: inherit;
            padding: 0
        }

        .prosa table {
            border-collapse: collapse;
            margin: 0 0 .9rem;
            font-size: .9em;
            display: block;
            overflow-x: auto
        }

        .prosa th,
        .prosa td {
            border: 1px solid rgb(226 232 240);
            padding: .4rem .6rem;
            text-align: left
        }

        .prosa th {
            background: rgb(248 250 252)
        }

        @keyframes blink {
            50% {
                opacity: 0
            }
        }

        .caret {
            display: inline-block;
            width: 7px;
            height: 16px;
            background: rgb(59 130 246);
            vertical-align: -3px;
            animation: blink 1s steps(2) infinite
        }

        @media (prefers-reduced-motion:reduce) {
            * {
                animation: none !important;
                transition: none !important
            }
        }
    </style>
</head>

<body class="h-screen overflow-hidden bg-gradient-to-br from-slate-200 via-blue-50 to-blue-200 font-sans text-slate-800 antialiased">

    @php
        $pemantik = [
            ['mengajar', 'Susun soal', 'Pilihan ganda + kunci + pembahasan', 'Buatkan 10 soal pilihan ganda beserta kunci jawaban dan pembahasan singkat untuk materi '],
            ['mengajar', 'Modul ajar', 'Satu pertemuan, lengkap dengan asesmen', 'Buatkan modul ajar satu pertemuan (45 menit) sesuai Kurikulum Merdeka untuk mata pelajaran '],
            ['mengajar', 'Sederhanakan materi', 'Ubah jadi bahasa yang ramah siswa', "Ringkas materi berikut menjadi bahasa yang mudah dipahami siswa SMP kelas 7, maksimal 200 kata:\n\n"],
            ['mengajar', 'Ide kegiatan', 'Aktivitas kelas tanpa biaya besar', 'Berikan 5 ide kegiatan pembelajaran yang menyenangkan tanpa alat mahal untuk topik '],
            ['penilaian', 'Umpan balik rapor', 'Catatan perkembangan yang membangun', "Bantu saya menulis catatan perkembangan dan umpan balik rapor untuk siswa dengan kondisi berikut:\n\n"],
            ['administrasi', 'Surat & administrasi', 'Draf surat resmi, notula, pengumuman', 'Buatkan draf surat resmi sekolah untuk keperluan '],
        ];
        $kategori = ['semua' => 'Semua', 'mengajar' => 'Mengajar', 'penilaian' => 'Penilaian', 'administrasi' => 'Administrasi'];
    @endphp

    {{-- Gutter luar hanya diatur di sini; anak-anaknya tidak menambah padding
         sendiri supaya jarak ke tiap tepi layar sama. --}}
    <div class="flex h-full gap-4 p-3 sm:p-4">

        {{-- ═══════════ SIDEBAR ═══════════ --}}
        <div id="tirai" class="hidden fixed inset-0 z-30 bg-slate-900/40 lg:hidden"></div>

        <aside id="sidebar"
            class="glass hidden fixed inset-y-0 left-0 z-40 w-64 max-w-[78vw] shrink-0 flex-col p-3 lg:flex lg:static lg:z-auto lg:max-w-none lg:rounded-3xl lg:border lg:border-white/80 lg:shadow-sm">

            <div class="rail-row flex items-center gap-2.5 px-2 py-1.5">
                {{-- Nib pena: mengikat kembali ke nama asli alatnya, "Tinta". Ikon
                     garis, bukan emoji, supaya sebaris dengan ikon navigasi. --}}
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-md shadow-blue-600/25">
                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                    </svg>
                </div>
                <div class="lbl min-w-0 flex-1">
                    <div class="truncate text-[13px] font-bold leading-tight tracking-tight text-slate-900">
                        {{ __('YPDH AI') }}</div>
                    <div class="truncate text-[9.5px] font-semibold uppercase tracking-[0.14em] text-slate-400">
                        {{ __('Asisten Guru') }}</div>
                </div>
                {{-- Di layar lebar tombol ini menciutkan sidebar; di layar kecil
                     mode rail tidak berlaku, jadi ia menutup laci. --}}
                <button type="button" id="btnRail" title="{{ __('Lebarkan / ciutkan sidebar') }}"
                    aria-label="{{ __('Lebarkan / ciutkan sidebar') }}"
                    class="lbl flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/70 hover:text-slate-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </button>
            </div>

            {{-- Saat rail, tombol pelebar pindah ke bawah logo agar tetap terjangkau --}}
            <button type="button" id="btnRailBuka" title="{{ __('Lebarkan / ciutkan sidebar') }}"
                aria-label="{{ __('Lebarkan / ciutkan sidebar') }}"
                class="rail-only mx-auto mt-3 hidden h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-white/70 hover:text-slate-900">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </button>

            <nav class="mt-5 space-y-0.5">
                <button type="button" data-view="chat"
                    class="tab on rail-row flex w-full items-center gap-2.5 rounded-xl px-2.5 py-2 text-[13px] font-semibold text-slate-500 transition hover:bg-white/50">
                    <svg class="h-[18px] w-[18px] shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm3.75 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM21 12c0 4.556-4.03 8.25-9 8.25a9.76 9.76 0 0 1-2.555-.337A5.97 5.97 0 0 1 5.41 20.97a5.97 5.97 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                    </svg>
                    <span class="lbl">{{ __('Ngobrol') }}</span>
                </button>

                @if ($imageReady)
                    <button type="button" data-view="gambar"
                        class="tab rail-row flex w-full items-center gap-2.5 rounded-xl px-2.5 py-2 text-[13px] font-semibold text-slate-500 transition hover:bg-white/50">
                        <svg class="h-[18px] w-[18px] shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        <span class="lbl">{{ __('Buat gambar') }}</span>
                    </button>
                @endif

                {{-- Garis rambut: "Ngobrol"/"Buat gambar" adalah moda, ini tindakan.
                     Pemisah lebih jujur daripada memberi label pada dua item. --}}
                <div class="rail-hide my-2 border-t border-white/70"></div>

                <button type="button" id="btnBaru"
                    class="rail-row flex w-full items-center gap-2.5 rounded-xl px-2.5 py-2 text-[13px] font-semibold text-blue-700 transition hover:bg-white/60">
                    <svg class="h-[18px] w-[18px] shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="lbl">{{ __('Percakapan baru') }}</span>
                </button>
            </nav>

            {{-- Riwayat: tersimpan di peramban ini saja, tidak dikirim ke server. --}}
            <div class="rail-hide mt-4 flex min-h-0 flex-1 flex-col">
                <div class="px-2.5 pb-1.5 text-[9.5px] font-bold uppercase tracking-[0.14em] text-slate-400">
                    {{ __('Riwayat') }}</div>
                <div id="daftarSesi" class="scroll-thin -mr-1 min-h-0 flex-1 space-y-0.5 overflow-y-auto pr-1"></div>
            </div>

            {{-- Nama model sengaja tidak diulang di sini: header area percakapan
                 sudah menampilkannya. --}}
            <div class="mt-auto pt-3">
                <div class="rail-stack flex gap-1.5">
                    <a href="{{ route('home') }}" title="{{ __('Beranda') }}"
                        class="rail-row flex flex-1 items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-[11px] font-semibold text-slate-500 transition hover:bg-white/60 hover:text-slate-900">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                        </svg>
                        <span class="lbl">{{ __('Beranda') }}</span>
                    </a>
                    <form method="POST" action="{{ route('ypdh-ai.lock') }}" class="flex-1">
                        @csrf
                        <button type="submit" title="{{ __('Kunci') }}"
                            class="rail-row flex w-full items-center justify-center gap-1.5 rounded-xl px-2.5 py-2 text-[11px] font-semibold text-slate-500 transition hover:bg-white/60 hover:text-slate-900">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            <span class="lbl">{{ __('Kunci') }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ═══════════ UTAMA ═══════════ --}}
        <main class="flex min-w-0 flex-1 flex-col gap-3">

            <div class="flex items-center gap-3">
                <button type="button" id="btnMenu"
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/70 ring-1 ring-white/80 lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="truncate text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                        {{ __('Asisten AI untuk Guru') }}</h1>
                    <p class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="h-2 w-2 rounded-full {{ $chatReady ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                        {{ $chatReady ? __('Terhubung ke :model', ['model' => $model]) : __('Pengaturan AI belum lengkap') }}
                    </p>
                </div>
            </div>

            <div class="flex min-h-0 flex-1 gap-4">

                {{-- ── Kolom percakapan ── --}}
                <section id="view-chat" class="view flex min-w-0 flex-1 flex-col">
                    <div class="glass flex min-h-0 flex-1 flex-col rounded-3xl border border-white/80 shadow-sm">

                        <div id="paper" class="scroll-thin min-h-0 flex-1 overflow-y-auto px-5 py-6 sm:px-7">
                            <div id="kosong" class="flex h-full flex-col items-center justify-center text-center">
                                <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-500 to-indigo-600 text-2xl shadow-xl shadow-blue-500/30">
                                    🖋️</div>
                                <h2 class="text-2xl font-light leading-snug text-slate-700 sm:text-3xl">
                                    {{ __('Mau dibantu apa') }}<br>{{ __('hari ini, Bu/Pak Guru?') }}</h2>
                                @unless ($chatReady)
                                    <p class="mt-5 max-w-sm rounded-2xl bg-amber-50 px-4 py-3 text-xs font-medium text-amber-800 ring-1 ring-amber-200">
                                        {{ __('Admin perlu mengisi API key dan model chat di Admin → Settings.') }}</p>
                                @endunless

                                {{-- Pemantik untuk layar kecil; layar lebar memakai panel kanan --}}
                                <div class="mt-7 grid w-full max-w-lg gap-2 sm:grid-cols-2 xl:hidden">
                                    @foreach (array_slice($pemantik, 0, 4) as [$kat, $judul, $ket, $isi])
                                        <button type="button" class="kartu rounded-2xl bg-white/80 p-3 text-left ring-1 ring-white/90 transition hover:ring-blue-300"
                                            data-isi="{{ $isi }}">
                                            <b class="block text-sm font-bold text-slate-800">{{ __($judul) }}</b>
                                            <i class="block text-[11px] not-italic text-slate-500">{{ __($ket) }}</i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-white/70 px-5 py-4 sm:px-7">
                            <div id="lampiran" class="mb-2 flex flex-wrap gap-2 empty:hidden"></div>

                            <div class="flex items-end gap-2 rounded-3xl bg-white/90 p-2 pl-3 shadow-sm ring-1 ring-white/90 focus-within:ring-2 focus-within:ring-blue-400">
                                <button type="button" id="btnFile" title="{{ __('Lampirkan berkas') }}"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </button>
                                <textarea id="tulis" rows="1" placeholder="{{ __('Tulis pertanyaan…') }}"
                                    class="max-h-40 flex-1 resize-none border-0 bg-transparent py-2 text-[15px] leading-relaxed text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-0"></textarea>
                                <button type="button" id="btnKirim"
                                    class="flex h-10 shrink-0 items-center gap-2 rounded-2xl bg-slate-900 px-4 text-sm font-bold text-white transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:bg-slate-400">
                                    <span id="kirimLabel">{{ __('Kirim') }}</span>
                                </button>
                            </div>
                            <p class="mt-2 text-[11px] font-medium text-slate-400">
                                {{ __('Enter kirim · Shift+Enter baris baru · PDF, DOCX, TXT, CSV, dan gambar didukung') }}</p>
                            <input type="file" id="inputFile" multiple hidden accept=".pdf,.docx,.txt,.md,.csv,.json,image/*">
                        </div>
                    </div>
                </section>

                {{-- ── Kolom gambar ── --}}
                @if ($imageReady)
                    <section id="view-gambar" class="view hide min-w-0 flex-1">
                        <div class="glass scroll-thin h-full overflow-y-auto rounded-3xl border border-white/80 p-5 shadow-sm sm:p-7">
                            <div class="mx-auto max-w-2xl">
                                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400"
                                    for="prompt">{{ __('Deskripsi gambar') }}</label>
                                <input type="text" id="prompt"
                                    class="w-full rounded-2xl border-0 bg-white/90 px-4 py-3 text-sm ring-1 ring-white/90 focus:ring-2 focus:ring-blue-400"
                                    placeholder="{{ __('Ilustrasi siklus air untuk poster kelas 5, gaya kartun sederhana') }}">

                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400"
                                            for="ukuran">{{ __('Ukuran') }}</label>
                                        <select id="ukuran"
                                            class="w-full rounded-2xl border-0 bg-white/90 px-4 py-3 text-sm ring-1 ring-white/90 focus:ring-2 focus:ring-blue-400">
                                            <option value="1024x1024">{{ __('Kotak') }} · 1024×1024</option>
                                            <option value="1024x1792">{{ __('Tegak') }} · 1024×1792</option>
                                            <option value="1792x1024">{{ __('Melebar') }} · 1792×1024</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-400"
                                            for="jumlah">{{ __('Jumlah') }}</label>
                                        <select id="jumlah"
                                            class="w-full rounded-2xl border-0 bg-white/90 px-4 py-3 text-sm ring-1 ring-white/90 focus:ring-2 focus:ring-blue-400">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap items-center gap-3">
                                    <button type="button" id="btnGambar"
                                        class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:bg-slate-400">
                                        {{ __('Buat gambar') }}</button>
                                    <span id="statusGambar" class="text-xs font-semibold"></span>
                                </div>

                                <div id="hasilGambar" class="mt-6 grid gap-4 sm:grid-cols-2"></div>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- ── Panel kanan: pemantik ── --}}
                <aside class="hidden w-80 shrink-0 xl:block">
                    <div class="glass flex h-full flex-col rounded-3xl border border-white/80 p-5 shadow-sm">
                        <h2 class="pb-3 text-base font-extrabold text-slate-900">{{ __('Pemantik') }}</h2>

                        <div class="flex flex-wrap gap-1.5 pb-3">
                            @foreach ($kategori as $slug => $label)
                                <button type="button" data-kat="{{ $slug }}"
                                    class="pil rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $loop->first ? 'bg-blue-600 text-white' : 'bg-white/80 text-slate-600 hover:bg-white' }}">
                                    {{ __($label) }}</button>
                            @endforeach
                        </div>

                        <div class="scroll-thin min-h-0 flex-1 space-y-2 overflow-y-auto pr-1">
                            @foreach ($pemantik as [$kat, $judul, $ket, $isi])
                                <button type="button"
                                    class="kartu w-full rounded-2xl bg-white/85 p-3 text-left ring-1 ring-white/90 transition hover:-translate-y-0.5 hover:ring-blue-300"
                                    data-kat="{{ $kat }}" data-isi="{{ $isi }}">
                                    <b class="block text-sm font-bold text-slate-800">{{ __($judul) }}</b>
                                    <i class="mt-0.5 block text-[11px] not-italic leading-snug text-slate-500">{{ __($ket) }}</i>
                                </button>
                            @endforeach
                        </div>

                        <p class="pt-3 text-center text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                            {{ __('YPDH AI') }}</p>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <script>
        /* =====================================================================
           YPDH AI — asisten untuk guru.
           Tidak ada API key di sini: halaman hanya bicara ke Laravel, Laravel
           yang memanggil gateway.
           ===================================================================== */
        (function() {
            const CHAT_URL = @json(route('ypdh-ai.chat'));
            const IMAGE_URL = @json(route('ypdh-ai.image'));
            const CSRF = document.querySelector('meta[name="csrf-token"]').content;

            @php
                $strings = [
                    'guru' => __('Guru'),
                    'asisten' => __('YPDH AI'),
                    'copy' => __('Salin jawaban'),
                    'copied' => __('Tersalin'),
                    'copyManual' => __('Salin manual dari layar'),
                    'writing' => __('Menulis…'),
                    'send' => __('Kirim'),
                    'failed' => __('Gagal menghubungi asisten'),
                    'emptyAnswer' => __('Jawaban kosong'),
                    'emptyAnswerHelp' => __('Gateway menjawab tanpa isi. Minta admin memeriksa nama model.'),
                    'fileFailed' => __('Berkas :name tidak bisa dibaca'),
                    'fileHelper' => __('gagal memuat pustaka pembaca berkas'),
                    'unreadable' => __('berkas tidak terbaca'),
                    'chars' => __(':n karakter'),
                    'image' => __('gambar'),
                    'removeAttachment' => __('Hapus lampiran'),
                    'withFile' => __('Tolong bantu saya dengan berkas ini.'),
                    'needPrompt' => __('Tulis dulu deskripsi gambarnya.'),
                    'drawing' => __('Menggambar…'),
                    'drawn' => __(':n gambar dibuat.'),
                    'download' => __('Unduh'),
                    'word' => __('Word'),
                    'pdf' => __('PDF'),
                    'excel' => __('Excel'),
                    'error' => __('Gagal: :message'),
                    'noHistory' => __('Belum ada percakapan tersimpan.'),
                    'untitled' => __('Tanpa judul'),
                    'deleteChat' => __('Hapus percakapan'),
                    'confirmDelete' => __('Hapus percakapan ":judul"?'),
                    'imgDropped' => __('[:n gambar tidak ikut disimpan di riwayat]'),
                    'full' => __('Penyimpanan peramban penuh — percakapan terlama dibuang.'),
                ];
            @endphp
            const T = @json($strings);

            function t(key, vars) {
                let s = T[key] || key;
                for (const k in (vars || {})) s = s.replaceAll(':' + k, vars[k]);
                return s;
            }

            const $ = s => document.querySelector(s);
            const esc = t => t.replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [c]));

            /* --------- Markdown ringan --------------------------------------
               Blok kode disimpan sementara sebagai token teks biasa (bukan byte
               kontrol) supaya berkas ini tetap teks murni. */
            function markdown(src) {
                const blok = [];
                let t = esc(src).replace(/```(\w*)\n?([\s\S]*?)```/g, (_, l, c) => {
                    blok.push('<pre><code>' + c.replace(/\n$/, '') + '</code></pre>');
                    return '@@BLOK' + (blok.length - 1) + '@@';
                });
                t = t.replace(/^\|(.+)\|[ \t]*$\n^\|[ :\-|]+\|[ \t]*$\n((?:^\|.*\|[ \t]*$\n?)*)/gm, (_, h, b) => {
                    const sel = r => r.split('|').slice(1, -1).map(c => c.trim());
                    const th = sel(h).map(c => '<th>' + c + '</th>').join('');
                    const tr = b.trim().split('\n').map(r => '<tr>' + sel(r).map(c => '<td>' + c + '</td>')
                        .join('') + '</tr>').join('');
                    return '<table><thead><tr>' + th + '</tr></thead><tbody>' + tr + '</tbody></table>';
                });
                t = t.replace(/^###?\s+(.+)$/gm, '<h3>$1</h3>')
                    .replace(/^##\s+(.+)$/gm, '<h2>$1</h2>')
                    .replace(/`([^`\n]+)`/g, '<code>$1</code>')
                    .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                    .replace(/(^|[\s(])\*([^*\n]+)\*/g, '$1<em>$2</em>');
                t = t.replace(/(?:^[ \t]*\d+[.)]\s+.+$\n?)+/gm, m =>
                    '<ol>' + m.trim().split('\n').map(l => '<li>' + l.replace(/^[ \t]*\d+[.)]\s+/, '') +
                        '</li>').join('') + '</ol>');
                t = t.replace(/(?:^[ \t]*[-*+]\s+.+$\n?)+/gm, m =>
                    '<ul>' + m.trim().split('\n').map(l => '<li>' + l.replace(/^[ \t]*[-*+]\s+/, '') +
                        '</li>').join('') + '</ul>');
                t = t.split(/\n{2,}/).map(p => /^\s*<(h2|h3|ul|ol|pre|table)/.test(p) || p.includes('@@BLOK') ?
                    p : (p.trim() ? '<p>' + p.replace(/\n/g, '<br>') + '</p>' : '')).join('');
                return t.replace(/@@BLOK(\d+)@@/g, (_, i) => blok[i]);
            }

            function galat(judul, isi) {
                const d = document.createElement('div');
                d.className =
                    'rounded-2xl bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-800 ring-1 ring-red-200';
                d.innerHTML = '<b class="block font-bold">' + esc(judul) + '</b>' + esc(isi);
                return d;
            }

            /* Pesan galat dari server dipakai apa adanya — server sudah
               memotongnya dan menyensor API key. */
            async function post(url, body) {
                const r = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify(body)
                });
                let j = {};
                try {
                    j = await r.json();
                } catch (e) {}
                if (!r.ok) throw new Error(j.message || ('HTTP ' + r.status));
                return j;
            }

            /* --------- Sidebar & tab ---------------------------------------- */
            const sidebar = $('#sidebar'),
                tirai = $('#tirai');
            // `hidden` (utility Tailwind), bukan `.hide` — `.hide` memakai !important
            // dan akan mengalahkan `lg:flex`, sehingga sidebar ikut hilang di desktop.
            const bukaMenu = on => {
                sidebar.classList.toggle('hidden', !on);
                tirai.classList.toggle('hidden', !on);
            };
            $('#btnMenu').onclick = () => bukaMenu(sidebar.classList.contains('hidden'));
            tirai.onclick = () => bukaMenu(false);

            /* Ciutkan/lebarkan sidebar di layar lebar; pilihannya diingat.
               Di layar kecil mode rail tidak berlaku (aturannya di dalam media
               query lg), jadi tombol yang sama dipakai untuk menutup laci. */
            const lebar = () => window.matchMedia('(min-width: 1024px)').matches;
            const railToggle = () => {
                if (!lebar()) {
                    bukaMenu(false);
                    return;
                }
                const rail = document.documentElement.classList.toggle('rail');
                try {
                    localStorage.setItem('ypdh_rail', rail ? '1' : '0');
                } catch (e) {}
            };
            $('#btnRail').onclick = railToggle;
            $('#btnRailBuka').onclick = railToggle;

            document.querySelectorAll('.tab').forEach(tab => tab.onclick = () => {
                document.querySelectorAll('.tab').forEach(x => x.classList.toggle('on', x === tab));
                document.querySelectorAll('.view').forEach(v =>
                    v.classList.toggle('hide', v.id !== 'view-' + tab.dataset.view));
                bukaMenu(false);
            });

            /* --------- Riwayat percakapan -----------------------------------
               Disimpan di localStorage peramban ini saja. Server tidak pernah
               menyimpan isi percakapan, jadi riwayat guru tidak tercampur —
               kecuali mereka memakai profil peramban yang sama. */
            const SESI_KEY = 'ypdh_ai_sesi_v1';
            const MAKS_SESI = 30;
            const daftarSesi = $('#daftarSesi');
            let sesiAktif = null;

            const bacaSesi = () => {
                try {
                    return JSON.parse(localStorage.getItem(SESI_KEY) || '[]');
                } catch (e) {
                    return [];
                }
            };

            function tulisSesi(list) {
                try {
                    localStorage.setItem(SESI_KEY, JSON.stringify(list));
                    return true;
                } catch (e) {
                    // Kuota penuh: buang yang terlama lalu coba sekali lagi.
                    try {
                        localStorage.setItem(SESI_KEY, JSON.stringify(list.slice(0, Math.max(1, list.length - 5))));
                        return true;
                    } catch (e2) {
                        return false;
                    }
                }
            }

            /* Gambar disimpan sebagai base64 dan cepat memenuhi kuota peramban,
               jadi yang masuk riwayat hanya teksnya. */
            function ringkas(m) {
                if (typeof m.content === 'string') return {
                    role: m.role,
                    content: m.content
                };
                const teks = m.content.filter(p => p.type === 'text').map(p => p.text).join('\n');
                const n = m.content.filter(p => p.type === 'image_url').length;
                return {
                    role: m.role,
                    content: teks + (n ? '\n\n' + t('imgDropped', {
                        n
                    }) : '')
                };
            }

            function judulDari(pesan) {
                const p = pesan.find(m => m.role === 'user');
                const teks = p ? (typeof p.content === 'string' ? p.content :
                    (p.content.find(x => x.type === 'text')?.text || '')) : '';
                const bersih = teks.replace(/\s+/g, ' ').trim();
                return bersih ? bersih.slice(0, 48) : T.untitled;
            }

            function simpanSesi() {
                if (!riwayat.length) return;
                if (!sesiAktif) sesiAktif = 'p' + Date.now();
                const list = bacaSesi().filter(s => s.id !== sesiAktif);
                list.unshift({
                    id: sesiAktif,
                    judul: judulDari(riwayat),
                    waktu: Date.now(),
                    pesan: riwayat.map(ringkas)
                });
                if (!tulisSesi(list.slice(0, MAKS_SESI))) console.warn(T.full);
                gambarSesi();
            }

            function waktuSingkat(ms) {
                const d = new Date(ms),
                    lewat = (Date.now() - ms) / 60000;
                if (lewat < 1) return 'baru saja';
                if (lewat < 60) return Math.floor(lewat) + ' mnt';
                if (lewat < 1440) return Math.floor(lewat / 60) + ' jam';
                return d.toLocaleDateString(@json(str_replace('_', '-', app()->getLocale())), {
                    day: '2-digit',
                    month: 'short'
                });
            }

            function gambarSesi() {
                const list = bacaSesi();
                daftarSesi.innerHTML = '';
                if (!list.length) {
                    const p = document.createElement('p');
                    p.className = 'px-2.5 py-1 text-[11px] leading-snug text-slate-400';
                    p.textContent = T.noHistory;
                    daftarSesi.appendChild(p);
                    return;
                }
                list.forEach(s => {
                    const row = document.createElement('div');
                    row.className = 'group flex items-center gap-1 rounded-xl pr-1 transition ' +
                        (s.id === sesiAktif ? 'bg-white shadow-sm' : 'hover:bg-white/60');

                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'min-w-0 flex-1 px-2.5 py-2 text-left';
                    b.onclick = () => bukaSesi(s.id);
                    const j = document.createElement('span');
                    j.className = 'block truncate text-[12px] font-semibold text-slate-700';
                    j.textContent = s.judul;
                    const w = document.createElement('span');
                    w.className = 'block text-[10px] font-medium text-slate-400';
                    w.textContent = waktuSingkat(s.waktu);
                    b.append(j, w);

                    const x = document.createElement('button');
                    x.type = 'button';
                    x.title = T.deleteChat;
                    x.setAttribute('aria-label', T.deleteChat);
                    x.className =
                        'flex h-6 w-6 shrink-0 items-center justify-center rounded-lg text-slate-300 opacity-0 transition hover:bg-red-50 hover:text-red-600 focus:opacity-100 group-hover:opacity-100';
                    x.innerHTML =
                        '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>';
                    x.onclick = e => {
                        e.stopPropagation();
                        hapusSesi(s.id, s.judul);
                    };

                    row.append(b, x);
                    daftarSesi.appendChild(row);
                });
            }

            function bukaSesi(id) {
                simpanSesi();
                const s = bacaSesi().find(x => x.id === id);
                if (!s) return;
                sesiAktif = s.id;
                riwayat = s.pesan.map(m => ({
                    role: m.role,
                    content: m.content
                }));
                paper.innerHTML = '';
                riwayat.forEach(m => {
                    const b = tambahPesan(m.role === 'user' ? 'me' : 'ai', m.content);
                    if (m.role === 'assistant') alatPesan(b, () => m.content);
                });
                gambarSesi();
                bukaMenu(false);
            }

            function hapusSesi(id, judul) {
                if (!confirm(t('confirmDelete', {
                        judul
                    }))) return;
                tulisSesi(bacaSesi().filter(s => s.id !== id));
                if (id === sesiAktif) mulaiBaru(true);
                else gambarSesi();
            }

            function mulaiBaru(lewatiSimpan) {
                if (!lewatiSimpan) simpanSesi();
                riwayat = [];
                sesiAktif = null;
                paper.innerHTML = kosongHtml;
                gambarSesi();
                bukaMenu(false);
                tulis.focus();
            }

            $('#btnBaru').onclick = () => mulaiBaru();

            /* --------- Pemantik & penyaring kategori ------------------------- */
            document.querySelectorAll('.pil').forEach(pil => pil.onclick = () => {
                const kat = pil.dataset.kat;
                document.querySelectorAll('.pil').forEach(x => {
                    const aktif = x === pil;
                    x.classList.toggle('bg-blue-600', aktif);
                    x.classList.toggle('text-white', aktif);
                    x.classList.toggle('bg-white/80', !aktif);
                    x.classList.toggle('text-slate-600', !aktif);
                });
                document.querySelectorAll('.kartu[data-kat]').forEach(k =>
                    k.classList.toggle('hide', kat !== 'semua' && k.dataset.kat !== kat));
            });

            /* --------- Lampiran berkas -------------------------------------- */
            let berkas = [];
            const daftar = $('#lampiran');

            function gambarDaftar() {
                daftar.innerHTML = '';
                berkas.forEach((f, i) => {
                    const c = document.createElement('div');
                    c.className =
                        'flex max-w-full items-center gap-2 rounded-full bg-white/90 py-1 pl-3 pr-1 text-xs ring-1 ring-white/90';
                    const jenis = f.jenis === 'gambar' ? T.image : t('chars', {
                        n: f.isi.length.toLocaleString('id-ID')
                    });
                    c.innerHTML = '<span class="truncate font-medium text-slate-700">' + esc(f.nama) +
                        '</span><em class="not-italic text-[10px] text-slate-400">' + esc(jenis) + '</em>';
                    const x = document.createElement('button');
                    x.type = 'button';
                    x.className =
                        'flex h-5 w-5 items-center justify-center rounded-full text-slate-400 hover:bg-red-50 hover:text-red-600';
                    x.textContent = '×';
                    x.title = T.removeAttachment;
                    x.onclick = () => {
                        berkas.splice(i, 1);
                        gambarDaftar();
                    };
                    c.appendChild(x);
                    daftar.appendChild(c);
                });
            }

            const muatSkrip = src => new Promise((res, rej) => {
                const s = document.createElement('script');
                s.src = src;
                s.onload = res;
                s.onerror = () => rej(new Error(T.fileHelper));
                document.head.appendChild(s);
            });

            // Berkas dibaca di peramban, tidak diunggah ke server sekolah.
            async function bacaPdf(file) {
                if (!window.pdfjsLib) {
                    await muatSkrip('https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js');
                    pdfjsLib.GlobalWorkerOptions.workerSrc =
                        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                }
                const pdf = await pdfjsLib.getDocument({
                    data: await file.arrayBuffer()
                }).promise;
                let out = '';
                for (let p = 1; p <= pdf.numPages; p++) {
                    const tc = await (await pdf.getPage(p)).getTextContent();
                    out += '\n[Halaman ' + p + ']\n' + tc.items.map(i => i.str).join(' ');
                }
                return out.trim();
            }

            async function bacaDocx(file) {
                if (!window.mammoth)
                    await muatSkrip('https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js');
                return (await mammoth.extractRawText({
                    arrayBuffer: await file.arrayBuffer()
                })).value.trim();
            }

            const keB64 = file => new Promise((res, rej) => {
                const r = new FileReader();
                r.onload = () => res(r.result.split(',')[1]);
                r.onerror = () => rej(new Error(T.unreadable));
                r.readAsDataURL(file);
            });

            $('#btnFile').onclick = () => $('#inputFile').click();
            $('#inputFile').onchange = async e => {
                for (const file of e.target.files) {
                    try {
                        if (file.type.startsWith('image/'))
                            berkas.push({
                                nama: file.name,
                                jenis: 'gambar',
                                data: await keB64(file),
                                mime: file.type
                            });
                        else if (/\.pdf$/i.test(file.name))
                            berkas.push({
                                nama: file.name,
                                jenis: 'teks',
                                isi: await bacaPdf(file)
                            });
                        else if (/\.docx$/i.test(file.name))
                            berkas.push({
                                nama: file.name,
                                jenis: 'teks',
                                isi: await bacaDocx(file)
                            });
                        else
                            berkas.push({
                                nama: file.name,
                                jenis: 'teks',
                                isi: await file.text()
                            });
                    } catch (err) {
                        paper.appendChild(galat(t('fileFailed', {
                            name: file.name
                        }), err.message));
                    }
                }
                e.target.value = '';
                gambarDaftar();
            };

            /* --------- Percakapan ------------------------------------------- */
            const paper = $('#paper'),
                tulis = $('#tulis'),
                kirim = $('#btnKirim'),
                kirimLabel = $('#kirimLabel');
            let riwayat = [],
                jalan = false;

            // Disimpan utuh supaya layar sambutan bisa dipasang lagi saat
            // "Percakapan baru" ditekan.
            const kosongHtml = $('#kosong')?.outerHTML || '';

            // Delegasi: kartu pemantik di layar sambutan lahir ulang tiap kali
            // percakapan direset, jadi jangan diikat satu per satu.
            document.addEventListener('click', e => {
                const k = e.target.closest('.kartu');
                if (!k) return;
                tulis.value = k.dataset.isi;
                tulis.focus();
                tumbuh();
                tulis.setSelectionRange(tulis.value.length, tulis.value.length);
            });

            function tumbuh() {
                tulis.style.height = 'auto';
                tulis.style.height = tulis.scrollHeight + 'px';
            }
            tulis.oninput = tumbuh;
            tulis.onkeydown = e => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    kirimPesan();
                }
            };
            kirim.onclick = kirimPesan;

            function tambahPesan(peran, teks) {
                $('#kosong')?.remove();
                const aku = peran === 'me';
                const d = document.createElement('div');
                d.className = 'mb-6 ' + (aku ? 'flex justify-end' : '');
                d.innerHTML = aku ?
                    '<div class="max-w-[85%] rounded-3xl rounded-br-lg bg-blue-600 px-4 py-3 text-[15px] leading-relaxed text-white whitespace-pre-wrap"></div>' :
                    '<div class="max-w-[92%]"><div class="mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">' +
                    esc(T.asisten) + '</div><div class="prosa text-slate-700"></div></div>';
                const b = d.querySelector(aku ? 'div' : '.prosa');
                if (aku) b.textContent = teks;
                else b.innerHTML = markdown(teks || '');
                paper.appendChild(d);
                paper.scrollTop = paper.scrollHeight;
                return b;
            }

            /* --------- Ekspor jawaban jadi berkas ----------------------------
               Jawaban AI sudah berbentuk markdown (judul, tabel, daftar), jadi
               HTML hasil render itulah yang dibungkus. Word dan Excel dibuka
               dari HTML berlabel MIME Office — cara yang didukung kedua program
               dan tidak menambah pustaka apa pun. Untuk PDF dipakai dialog
               cetak peramban: hasilnya paling rapi dan tidak perlu pustaka. */
            const NAMA_TAK_AMAN = /[\\/:*?"<>|]+/g;

            function namaBerkas(bubble, ext) {
                const h = bubble.querySelector('h2, h3');
                const dasar = (h?.textContent || bubble.textContent || 'ypdh-ai')
                    .replace(/\s+/g, ' ').trim().slice(0, 50).replace(NAMA_TAK_AMAN, '-');
                return (dasar || 'ypdh-ai') + '.' + ext;
            }

            // Times New Roman 12pt: lazim untuk dokumen sekolah di Indonesia.
            function dokumen(isi) {
                return '<html xmlns:o="urn:schemas-microsoft-com:office:office" ' +
                    'xmlns:w="urn:schemas-microsoft-com:office:word"><head><meta charset="utf-8">' +
                    '<style>' +
                    '@page{size:A4;margin:2.5cm 2cm}' +
                    'body{font-family:"Times New Roman",serif;font-size:12pt;line-height:1.5;color:#000}' +
                    'h2{font-size:14pt;margin:18pt 0 6pt}h3{font-size:12.5pt;margin:14pt 0 5pt}' +
                    'p{margin:0 0 10pt}ul,ol{margin:0 0 10pt;padding-left:22pt}li{margin:0 0 4pt}' +
                    'table{border-collapse:collapse;width:100%;margin:0 0 12pt}' +
                    'th,td{border:1px solid #000;padding:5pt 7pt;text-align:left;vertical-align:top}' +
                    'th{background:#eee;font-weight:bold}' +
                    'pre{font-family:Consolas,monospace;font-size:10pt;background:#f4f4f4;padding:8pt;' +
                    'border:1px solid #ddd;white-space:pre-wrap}' +
                    'code{font-family:Consolas,monospace;font-size:10.5pt}' +
                    '</style></head><body>' + isi + '</body></html>';
            }

            function unduh(nama, isi, mime) {
                // BOM supaya Word/Excel membaca UTF-8 (huruf beraksen tidak rusak).
                const url = URL.createObjectURL(new Blob(['﻿' + isi], {
                    type: mime
                }));
                const a = document.createElement('a');
                a.href = url;
                a.download = nama;
                a.click();
                setTimeout(() => URL.revokeObjectURL(url), 2000);
            }

            function cetak(isi) {
                const f = document.createElement('iframe');
                f.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
                document.body.appendChild(f);
                f.srcdoc = dokumen(isi);
                f.onload = () => {
                    const w = f.contentWindow;
                    w.onafterprint = () => f.remove();
                    w.focus();
                    w.print();
                    setTimeout(() => f.isConnected && f.remove(), 60000);
                };
            }

            function alatPesan(bubble, ambil) {
                const bar = document.createElement('div');
                bar.className = 'mt-2 flex flex-wrap items-center gap-x-3 gap-y-1';

                const tombol = (teks, aksi) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className =
                        'text-[10px] font-bold uppercase tracking-wider text-slate-400 underline underline-offset-4 transition hover:text-blue-600';
                    b.textContent = teks;
                    b.onclick = () => aksi(b);
                    bar.appendChild(b);
                    return b;
                };

                tombol(T.copy, async b => {
                    try {
                        await navigator.clipboard.writeText(ambil());
                        b.textContent = T.copied;
                    } catch {
                        b.textContent = T.copyManual;
                    }
                    setTimeout(() => b.textContent = T.copy, 1600);
                });

                tombol(T.word, () => unduh(namaBerkas(bubble, 'doc'), dokumen(bubble.innerHTML),
                    'application/msword'));

                tombol(T.pdf, () => cetak(bubble.innerHTML));

                // Excel hanya berguna kalau jawabannya memang punya tabel.
                const tabel = bubble.querySelectorAll('table');
                if (tabel.length) {
                    tombol(T.excel, () => unduh(namaBerkas(bubble, 'xls'),
                        dokumen([...tabel].map(t => t.outerHTML).join('<br>')),
                        'application/vnd.ms-excel'));
                }

                bubble.parentElement.appendChild(bar);
            }

            async function kirimPesan() {
                if (jalan) return;
                const teks = tulis.value.trim();
                if (!teks && !berkas.length) return;

                const lampiranTeks = berkas.filter(f => f.jenis === 'teks')
                    .map(f => '\n\n=== Isi berkas: ' + f.nama + ' ===\n' + f.isi.slice(0, 60000)).join('');
                const lampiranGambar = berkas.filter(f => f.jenis === 'gambar');
                const tampil = teks + (berkas.length ? '\n\n📎 ' + berkas.map(f => f.nama).join(', ') : '');

                let isi;
                if (lampiranGambar.length) {
                    isi = [{
                        type: 'text',
                        text: (teks || T.withFile) + lampiranTeks
                    }];
                    lampiranGambar.forEach(g => isi.push({
                        type: 'image_url',
                        image_url: {
                            url: 'data:' + g.mime + ';base64,' + g.data
                        }
                    }));
                } else {
                    isi = (teks || T.withFile) + lampiranTeks;
                }

                tambahPesan('me', tampil);
                riwayat.push({
                    role: 'user',
                    content: isi
                });
                berkas = [];
                gambarDaftar();
                tulis.value = '';
                tumbuh();

                jalan = true;
                kirim.disabled = true;
                kirimLabel.textContent = T.writing;
                const bubble = tambahPesan('ai', '');
                bubble.innerHTML = '<span class="caret"></span>';

                try {
                    // Riwayat dipangkas: 20 pesan terakhir cukup untuk konteks dan
                    // menjaga biaya per panggilan tetap masuk akal.
                    const j = await post(CHAT_URL, {
                        messages: riwayat.slice(-20)
                    });
                    const hasil = j.content || '';
                    if (!hasil.trim()) {
                        bubble.innerHTML = '';
                        bubble.appendChild(galat(T.emptyAnswer, T.emptyAnswerHelp));
                    } else {
                        bubble.innerHTML = markdown(hasil);
                        riwayat.push({
                            role: 'assistant',
                            content: hasil
                        });
                        alatPesan(bubble, () => hasil);
                        simpanSesi();
                    }
                } catch (e) {
                    riwayat.pop(); // buang pesan gagal supaya tidak terkirim ulang
                    bubble.innerHTML = '';
                    bubble.appendChild(galat(T.failed, e.message));
                } finally {
                    jalan = false;
                    kirim.disabled = false;
                    kirimLabel.textContent = T.send;
                    paper.scrollTop = paper.scrollHeight;
                }
            }

            /* --------- Buat gambar ------------------------------------------ */
            const btnGambar = $('#btnGambar');
            if (btnGambar) {
                btnGambar.onclick = async () => {
                    const s = $('#statusGambar'),
                        p = $('#prompt').value.trim();
                    const warna = ok => s.className = 'text-xs font-semibold ' + (ok ? 'text-emerald-600' :
                        'text-red-600');
                    if (!p) {
                        warna(false);
                        s.textContent = T.needPrompt;
                        return;
                    }
                    s.className = 'text-xs font-semibold text-slate-500';
                    s.textContent = T.drawing;
                    btnGambar.disabled = true;
                    try {
                        const j = await post(IMAGE_URL, {
                            prompt: p,
                            count: +$('#jumlah').value,
                            size: $('#ukuran').value
                        });
                        (j.images || []).forEach((url, i) => {
                            const f = document.createElement('figure');
                            f.className =
                                'overflow-hidden rounded-2xl bg-white/90 ring-1 ring-white/90';
                            const img = document.createElement('img');
                            img.alt = p;
                            img.src = url;
                            img.className = 'w-full';
                            const cap = document.createElement('figcaption');
                            cap.className =
                                'flex items-start justify-between gap-3 px-3 py-2 text-[11px] text-slate-500';
                            const span = document.createElement('span');
                            span.textContent = p.slice(0, 60);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'ypdh-ai-' + i + '.png';
                            a.className = 'shrink-0 font-bold text-blue-600 hover:underline';
                            a.textContent = T.download;
                            cap.append(span, a);
                            f.append(img, cap);
                            $('#hasilGambar').prepend(f);
                        });
                        warna(true);
                        s.textContent = t('drawn', {
                            n: (j.images || []).length
                        });
                    } catch (e) {
                        warna(false);
                        s.textContent = t('error', {
                            message: e.message
                        });
                    } finally {
                        btnGambar.disabled = false;
                    }
                };
            }

            gambarSesi();
            tulis.focus();
        })();
    </script>
</body>

</html>
