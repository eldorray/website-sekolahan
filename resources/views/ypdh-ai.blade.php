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
    <link
        href="https://fonts.googleapis.com/css2?family=Zilla+Slab:wght@500;600;700&family=Karla:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --kertas: #FCFBF6;
            --kertas-2: #F2F0E6;
            --garis: #CBD9EA;
            --tinta: #14284D;
            --tinta-2: #55658A;
            --koreksi: #B6362B;
            --stabilo: #F7E259;
            --hijau: #2F6B4F;
            --radius: 10px;
            --display: "Zilla Slab", Georgia, serif;
            --body: "Karla", system-ui, -apple-system, sans-serif;
            --mono: "IBM Plex Mono", ui-monospace, monospace;
        }

        * {
            box-sizing: border-box
        }

        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            font-family: var(--body);
            color: var(--tinta);
            background: var(--kertas);
            -webkit-font-smoothing: antialiased;
        }

        button {
            font-family: inherit;
            cursor: pointer
        }

        :focus-visible {
            outline: 2px solid var(--koreksi);
            outline-offset: 2px
        }

        /* ---------- Kerangka ---------- */
        .app {
            max-width: 940px;
            margin: 0 auto;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--garis);
            border-right: 1px solid var(--garis);
            background: var(--kertas)
        }

        header {
            padding: 18px 22px 14px;
            border-bottom: 1px solid var(--garis);
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            background: var(--kertas);
            position: sticky;
            top: 0;
            z-index: 20
        }

        .brand {
            flex: 1;
            min-width: 180px
        }

        .eyebrow {
            font-family: var(--mono);
            font-size: 10.5px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--tinta-2);
            margin: 0 0 2px
        }

        h1 {
            font-family: var(--display);
            font-weight: 700;
            font-size: 30px;
            letter-spacing: -.01em;
            margin: 0;
            line-height: 1
        }

        h1 .dot {
            color: var(--koreksi)
        }

        .head-actions {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .head-actions form {
            display: contents
        }

        .chip {
            font-family: var(--mono);
            font-size: 11px;
            color: var(--tinta-2);
            background: var(--kertas-2);
            border: 1px solid var(--garis);
            border-radius: 999px;
            padding: 5px 11px;
            max-width: 210px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .icon-btn {
            background: none;
            border: 1px solid var(--garis);
            border-radius: var(--radius);
            padding: 7px 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--tinta);
            text-decoration: none;
            display: inline-block;
            transition: background .15s, border-color .15s
        }

        .icon-btn:hover {
            background: var(--kertas-2);
            border-color: var(--tinta-2)
        }

        /* ---------- Tab ---------- */
        nav {
            display: flex;
            gap: 4px;
            padding: 10px 22px 0;
            border-bottom: 1px solid var(--garis)
        }

        .tab {
            position: relative;
            background: none;
            border: 0;
            padding: 8px 4px 12px;
            margin-right: 20px;
            font-size: 15px;
            font-weight: 600;
            color: var(--tinta-2)
        }

        .tab span {
            position: relative;
            z-index: 1
        }

        .tab.on {
            color: var(--tinta)
        }

        .tab.on span::after {
            content: "";
            position: absolute;
            left: -4px;
            right: -4px;
            bottom: -1px;
            height: 9px;
            background: var(--stabilo);
            z-index: -1;
            border-radius: 2px
        }

        .tab.on::after {
            content: "";
            position: absolute;
            left: 0;
            right: 20px;
            bottom: -1px;
            height: 2px;
            background: var(--tinta)
        }

        /* ---------- Panel percakapan: kertas bergaris ---------- */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0
        }

        .view {
            flex: 1;
            display: none;
            flex-direction: column;
            min-height: 0
        }

        .view.on {
            display: flex
        }

        .paper {
            flex: 1;
            overflow-y: auto;
            padding: 26px 22px 30px 60px;
            position: relative;
            background-image: repeating-linear-gradient(to bottom, transparent 0, transparent 31px, var(--garis) 31px, var(--garis) 32px);
            background-position: 0 26px
        }

        .paper::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 44px;
            width: 1px;
            background: var(--koreksi);
            opacity: .45
        }

        /* ---------- Pesan ---------- */
        .msg {
            margin: 0 0 22px;
            max-width: 74ch
        }

        .who {
            font-family: var(--mono);
            font-size: 10.5px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--tinta-2);
            margin-bottom: 5px
        }

        .msg.me .bubble {
            background: var(--kertas-2);
            border-left: 3px solid var(--koreksi);
            padding: 11px 14px;
            border-radius: 0 var(--radius) var(--radius) 0
        }

        .bubble {
            font-size: 15.5px;
            line-height: 32px
        }

        .bubble p {
            margin: 0 0 32px
        }

        .bubble p:last-child {
            margin-bottom: 0
        }

        .bubble h2,
        .bubble h3 {
            font-family: var(--display);
            font-size: 18px;
            margin: 32px 0 0;
            line-height: 32px
        }

        .bubble ul,
        .bubble ol {
            margin: 0 0 32px;
            padding-left: 22px
        }

        .bubble li {
            line-height: 32px
        }

        .bubble code {
            font-family: var(--mono);
            font-size: 13.5px;
            background: var(--kertas-2);
            padding: 1px 5px;
            border-radius: 4px
        }

        .bubble pre {
            background: #101E36;
            color: #E8EEF7;
            padding: 14px 16px;
            border-radius: var(--radius);
            overflow-x: auto;
            line-height: 1.55;
            margin: 0 0 32px
        }

        .bubble pre code {
            background: none;
            color: inherit;
            padding: 0;
            font-size: 13px
        }

        .bubble strong {
            font-weight: 700
        }

        .bubble table {
            border-collapse: collapse;
            margin: 0 0 32px;
            font-size: 14px
        }

        .bubble th,
        .bubble td {
            border: 1px solid var(--garis);
            padding: 6px 10px;
            line-height: 1.5;
            text-align: left
        }

        .bubble th {
            background: var(--kertas-2)
        }

        .msg-tools {
            margin-top: 6px
        }

        .mini {
            background: none;
            border: 0;
            padding: 2px 0;
            font-family: var(--mono);
            font-size: 10.5px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--tinta-2);
            text-decoration: underline;
            text-underline-offset: 3px
        }

        .mini:hover {
            color: var(--koreksi)
        }

        .caret {
            display: inline-block;
            width: 8px;
            height: 17px;
            background: var(--tinta);
            vertical-align: -3px;
            animation: blink 1s steps(2) infinite
        }

        @keyframes blink {
            50% {
                opacity: 0
            }
        }

        /* ---------- Layar kosong / pemantik ---------- */
        .kosong {
            max-width: 70ch
        }

        .kosong h2 {
            font-family: var(--display);
            font-weight: 600;
            font-size: 23px;
            margin: 0 0 6px;
            line-height: 32px
        }

        .kosong p {
            font-size: 15px;
            line-height: 32px;
            color: var(--tinta-2);
            margin: 0 0 24px
        }

        .pemantik {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 10px
        }

        .kartu {
            text-align: left;
            background: var(--kertas);
            border: 1px solid var(--garis);
            border-radius: var(--radius);
            padding: 12px 14px;
            transition: border-color .15s, transform .15s
        }

        .kartu:hover {
            border-color: var(--tinta);
            transform: translateY(-2px)
        }

        .kartu b {
            display: block;
            font-family: var(--display);
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 2px
        }

        .kartu i {
            font-style: normal;
            font-size: 12.5px;
            color: var(--tinta-2);
            line-height: 1.45;
            display: block
        }

        /* ---------- Composer ---------- */
        .composer {
            border-top: 1px solid var(--garis);
            padding: 12px 22px 18px;
            background: var(--kertas)
        }

        .lampiran {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px
        }

        .lampiran:empty {
            display: none
        }

        .file-chip {
            display: flex;
            align-items: center;
            gap: 7px;
            background: var(--kertas-2);
            border: 1px solid var(--garis);
            border-radius: 999px;
            padding: 4px 6px 4px 11px;
            font-size: 12.5px;
            max-width: 100%
        }

        .file-chip em {
            font-style: normal;
            font-family: var(--mono);
            font-size: 10px;
            color: var(--tinta-2)
        }

        .file-chip button {
            background: none;
            border: 0;
            font-size: 15px;
            line-height: 1;
            color: var(--tinta-2);
            padding: 0 4px
        }

        .file-chip button:hover {
            color: var(--koreksi)
        }

        .baris {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            border: 1px solid var(--tinta-2);
            border-radius: var(--radius);
            padding: 8px 8px 8px 12px;
            background: var(--kertas);
            transition: box-shadow .15s
        }

        .baris:focus-within {
            box-shadow: 0 0 0 3px rgba(20, 40, 77, .12)
        }

        textarea {
            flex: 1;
            border: 0;
            background: none;
            resize: none;
            font-family: inherit;
            font-size: 15.5px;
            line-height: 1.5;
            color: var(--tinta);
            max-height: 180px;
            padding: 4px 0
        }

        textarea:focus {
            outline: none
        }

        .kirim {
            background: var(--tinta);
            color: var(--kertas);
            border: 0;
            border-radius: 8px;
            padding: 9px 16px;
            font-size: 14px;
            font-weight: 700
        }

        .kirim:hover {
            background: var(--koreksi)
        }

        .kirim:disabled {
            background: var(--tinta-2);
            opacity: .5;
            cursor: not-allowed
        }

        .hint {
            font-family: var(--mono);
            font-size: 10.5px;
            color: var(--tinta-2);
            margin-top: 8px;
            letter-spacing: .04em
        }

        /* ---------- Gambar ---------- */
        .gambar-view {
            padding: 26px 22px;
            overflow-y: auto
        }

        .lebar {
            max-width: 70ch
        }

        label.f {
            display: block;
            font-family: var(--mono);
            font-size: 10.5px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--tinta-2);
            margin: 0 0 6px
        }

        input[type=text],
        select {
            width: 100%;
            font-family: var(--body);
            font-size: 14.5px;
            color: var(--tinta);
            background: var(--kertas);
            border: 1px solid var(--garis);
            border-radius: var(--radius);
            padding: 10px 12px
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--tinta)
        }

        .dua {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        .hasil {
            margin-top: 22px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 14px
        }

        .hasil figure {
            margin: 0;
            border: 1px solid var(--garis);
            border-radius: var(--radius);
            overflow: hidden;
            background: var(--kertas-2)
        }

        .hasil img {
            width: 100%;
            display: block
        }

        .hasil figcaption {
            padding: 9px 11px;
            font-size: 12.5px;
            line-height: 1.45;
            color: var(--tinta-2);
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start
        }

        .hasil figcaption a {
            color: var(--tinta);
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap
        }

        .aksi {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap
        }

        .status {
            font-family: var(--mono);
            font-size: 11.5px;
            color: var(--tinta-2)
        }

        .status.ok {
            color: var(--hijau)
        }

        .status.bad {
            color: var(--koreksi)
        }

        .galat {
            border-left: 3px solid var(--koreksi);
            background: #FDF2F0;
            padding: 11px 14px;
            font-size: 14px;
            line-height: 1.55;
            border-radius: 0 var(--radius) var(--radius) 0;
            max-width: 70ch
        }

        .galat b {
            display: block;
            margin-bottom: 2px
        }

        @media (max-width:640px) {
            header {
                padding: 14px 16px 12px
            }

            h1 {
                font-size: 25px
            }

            nav {
                padding: 8px 16px 0
            }

            .paper {
                padding: 20px 16px 24px 44px;
                background-position: 0 20px
            }

            .paper::before {
                left: 30px
            }

            .composer,
            .gambar-view {
                padding-left: 16px;
                padding-right: 16px
            }

            .dua {
                grid-template-columns: 1fr
            }

            .chip {
                display: none
            }
        }

        @media (prefers-reduced-motion:reduce) {
            * {
                animation: none !important;
                transition: none !important
            }
        }
    </style>
</head>

<body>
    <div class="app">
        <header>
            <div class="brand">
                <p class="eyebrow">{{ __('Asisten AI ruang guru') }}</p>
                <h1>{{ __('YPDH AI') }}<span class="dot">.</span></h1>
            </div>
            <div class="head-actions">
                <span class="chip">{{ $model ?: __('model belum diatur') }}</span>
                <a class="icon-btn" href="{{ route('home') }}">{{ __('Beranda') }}</a>
                <form method="POST" action="{{ route('ypdh-ai.lock') }}">
                    @csrf
                    <button type="submit" class="icon-btn">🔒 {{ __('Kunci') }}</button>
                </form>
            </div>
        </header>

        <nav>
            <button class="tab on" data-view="chat"><span>{{ __('Ngobrol') }}</span></button>
            @if ($imageReady)
                <button class="tab" data-view="gambar"><span>{{ __('Buat gambar') }}</span></button>
            @endif
        </nav>

        <main>
            {{-- ============ NGOBROL ============ --}}
            <section class="view on" id="view-chat">
                <div class="paper" id="paper">
                    <div class="kosong" id="kosong">
                        <h2>{{ __('Selamat datang, Bu/Pak Guru.') }}</h2>
                        <p>{{ __('Tanya apa saja, atau lampirkan berkas (PDF, Word, teks, gambar) untuk dibaca dan diolah. Pilih salah satu pemantik di bawah untuk memulai.') }}
                        </p>
                        @unless ($chatReady)
                            <div class="galat" style="margin-bottom:24px">
                                <b>{{ __('Pengaturan AI belum lengkap') }}</b>
                                {{ __('Admin perlu mengisi API key dan model chat di Admin → Settings.') }}
                            </div>
                        @endunless
                        @php
                            $pemantik = [
                                ['Susun soal', 'Pilihan ganda + kunci + pembahasan', 'Buatkan 10 soal pilihan ganda beserta kunci jawaban dan pembahasan singkat untuk materi '],
                                ['Modul ajar', 'Satu pertemuan, lengkap dengan asesmen', 'Buatkan modul ajar satu pertemuan (45 menit) sesuai Kurikulum Merdeka untuk mata pelajaran '],
                                ['Sederhanakan materi', 'Ubah jadi bahasa yang ramah siswa', "Ringkas materi berikut menjadi bahasa yang mudah dipahami siswa SMP kelas 7, maksimal 200 kata:\n\n"],
                                ['Surat & administrasi', 'Draf surat resmi, notula, pengumuman', 'Buatkan draf surat resmi sekolah untuk keperluan '],
                                ['Ide kegiatan', 'Aktivitas kelas tanpa biaya besar', 'Berikan 5 ide kegiatan pembelajaran yang menyenangkan tanpa alat mahal untuk topik '],
                                ['Umpan balik rapor', 'Catatan perkembangan yang membangun', "Bantu saya menulis catatan perkembangan dan umpan balik rapor untuk siswa dengan kondisi berikut:\n\n"],
                            ];
                        @endphp
                        <div class="pemantik">
                            @foreach ($pemantik as [$judul, $ket, $isi])
                                <button class="kartu" data-isi="{{ $isi }}">
                                    <b>{{ __($judul) }}</b><i>{{ __($ket) }}</i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="composer">
                    <div class="lampiran" id="lampiran"></div>
                    <div class="baris">
                        <textarea id="tulis" rows="1" placeholder="{{ __('Tulis pertanyaan…') }}"></textarea>
                        <button class="icon-btn" id="btnFile" title="{{ __('Lampirkan berkas') }}">{{ __('Berkas') }}</button>
                        <button class="kirim" id="btnKirim">{{ __('Kirim') }}</button>
                    </div>
                    <p class="hint">{{ __('Enter kirim · Shift+Enter baris baru · PDF, DOCX, TXT, CSV, dan gambar didukung') }}</p>
                    <input type="file" id="inputFile" multiple hidden accept=".pdf,.docx,.txt,.md,.csv,.json,image/*">
                </div>
            </section>

            {{-- ============ GAMBAR ============ --}}
            @if ($imageReady)
                <section class="view gambar-view" id="view-gambar">
                    <div class="lebar">
                        <label class="f" for="prompt">{{ __('Deskripsi gambar') }}</label>
                        <input type="text" id="prompt"
                            placeholder="{{ __('Ilustrasi siklus air untuk poster kelas 5, gaya kartun sederhana') }}">
                        <div class="dua" style="margin-top:12px">
                            <div>
                                <label class="f" for="ukuran">{{ __('Ukuran') }}</label>
                                <select id="ukuran">
                                    <option value="1024x1024">{{ __('Kotak') }} · 1024×1024</option>
                                    <option value="1024x1792">{{ __('Tegak') }} · 1024×1792</option>
                                    <option value="1792x1024">{{ __('Melebar') }} · 1792×1024</option>
                                </select>
                            </div>
                            <div>
                                <label class="f" for="jumlah">{{ __('Jumlah') }}</label>
                                <select id="jumlah">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                </select>
                            </div>
                        </div>
                        <div class="aksi" style="margin-top:14px">
                            <button class="kirim" id="btnGambar">{{ __('Buat gambar') }}</button>
                            <span class="status" id="statusGambar"></span>
                        </div>
                        <div class="hasil" id="hasilGambar"></div>
                    </div>
                </section>
            @endif
        </main>
    </div>

    <script>
        /* =====================================================================
           YPDH AI — asisten untuk guru.
           Bedanya dengan prototipe: tidak ada API key di sini. Halaman ini hanya
           bicara ke Laravel, Laravel yang memanggil gateway.
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
                    'error' => __('Gagal: :message'),
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

            /* --------- Markdown ringan -------------------------------------- */
            function markdown(src) {
                const blok = [];
                let t = esc(src).replace(/```(\w*)\n?([\s\S]*?)```/g, (_, l, c) => {
                    blok.push('<pre><code>' + c.replace(/\n$/, '') + '</code></pre>');
                    return '\u0000' + (blok.length - 1) + '\u0000';
                });
                t = t.replace(/^\|(.+)\|[ \t]*$\n^\|[ :\-|]+\|[ \t]*$\n((?:^\|.*\|[ \t]*$\n?)*)/gm, (_, h, b) => {
                    const sel = r => r.split('|').slice(1, -1).map(c => c.trim());
                    const th = sel(h).map(c => '<th>' + c + '</th>').join('');
                    const tr = b.trim().split('\n').map(r => '<tr>' + sel(r).map(c => '<td>' + c + '</td>').join(
                        '') + '</tr>').join('');
                    return '<table><thead><tr>' + th + '</tr></thead><tbody>' + tr + '</tbody></table>';
                });
                t = t.replace(/^###?\s+(.+)$/gm, '<h3>$1</h3>')
                    .replace(/^##\s+(.+)$/gm, '<h2>$1</h2>')
                    .replace(/`([^`\n]+)`/g, '<code>$1</code>')
                    .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
                    .replace(/(^|[\s(])\*([^*\n]+)\*/g, '$1<em>$2</em>');
                t = t.replace(/(?:^[ \t]*\d+[.)]\s+.+$\n?)+/gm, m =>
                    '<ol>' + m.trim().split('\n').map(l => '<li>' + l.replace(/^[ \t]*\d+[.)]\s+/, '') + '</li>')
                    .join('') + '</ol>');
                t = t.replace(/(?:^[ \t]*[-*+]\s+.+$\n?)+/gm, m =>
                    '<ul>' + m.trim().split('\n').map(l => '<li>' + l.replace(/^[ \t]*[-*+]\s+/, '') + '</li>')
                    .join('') + '</ul>');
                t = t.split(/\n{2,}/).map(p => /^\s*<(h2|h3|ul|ol|pre|table|\u0000)/.test(p) || p.includes('\u0000') ?
                    p : (p.trim() ? '<p>' + p.replace(/\n/g, '<br>') + '</p>' : '')).join('');
                return t.replace(/\u0000(\d+)\u0000/g, (_, i) => blok[i]);
            }

            function galat(judul, isi) {
                const d = document.createElement('div');
                d.className = 'galat';
                d.innerHTML = '<b>' + esc(judul) + '</b>' + esc(isi);
                return d;
            }

            /* Kirim ke Laravel. Pesan galat dari server dipakai apa adanya —
               server sudah memotongnya dan tidak pernah menyertakan API key. */
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

            /* --------- Tab --------------------------------------------------- */
            document.querySelectorAll('.tab').forEach(tab => tab.onclick = () => {
                document.querySelectorAll('.tab').forEach(x => x.classList.toggle('on', x === tab));
                document.querySelectorAll('.view').forEach(v =>
                    v.classList.toggle('on', v.id === 'view-' + tab.dataset.view));
            });

            /* --------- Lampiran berkas -------------------------------------- */
            let berkas = [];
            const daftar = $('#lampiran');

            function gambarDaftar() {
                daftar.innerHTML = '';
                berkas.forEach((f, i) => {
                    const c = document.createElement('div');
                    c.className = 'file-chip';
                    const jenis = f.jenis === 'gambar' ? T.image : t('chars', {
                        n: f.isi.length.toLocaleString('id-ID')
                    });
                    c.innerHTML = '<span>' + esc(f.nama) + '</span><em>' + esc(jenis) + '</em>';
                    const x = document.createElement('button');
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
                kirim = $('#btnKirim');
            let riwayat = [],
                jalan = false;

            document.querySelectorAll('.kartu').forEach(k => k.onclick = () => {
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
                const kosong = $('#kosong');
                if (kosong) kosong.remove();
                const d = document.createElement('div');
                d.className = 'msg' + (peran === 'me' ? ' me' : '');
                d.innerHTML = '<div class="who">' + esc(peran === 'me' ? T.guru : T.asisten) + '</div>' +
                    '<div class="bubble"></div>';
                const b = d.querySelector('.bubble');
                if (peran === 'me') b.textContent = teks;
                else b.innerHTML = markdown(teks || '');
                paper.appendChild(d);
                paper.scrollTop = paper.scrollHeight;
                return b;
            }

            function tombolSalin(bubble, ambil) {
                const w = document.createElement('div');
                w.className = 'msg-tools';
                const b = document.createElement('button');
                b.className = 'mini';
                b.textContent = T.copy;
                b.onclick = async () => {
                    try {
                        await navigator.clipboard.writeText(ambil());
                        b.textContent = T.copied;
                    } catch {
                        b.textContent = T.copyManual;
                    }
                    setTimeout(() => b.textContent = T.copy, 1600);
                };
                w.appendChild(b);
                bubble.parentElement.appendChild(w);
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
                kirim.textContent = T.writing;
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
                        tombolSalin(bubble, () => hasil);
                    }
                } catch (e) {
                    riwayat.pop(); // buang pesan yang gagal supaya tidak terkirim ulang
                    bubble.innerHTML = '';
                    bubble.appendChild(galat(T.failed, e.message));
                } finally {
                    jalan = false;
                    kirim.disabled = false;
                    kirim.textContent = T.send;
                    paper.scrollTop = paper.scrollHeight;
                }
            }

            /* --------- Buat gambar ------------------------------------------ */
            const btnGambar = $('#btnGambar');
            if (btnGambar) {
                btnGambar.onclick = async () => {
                    const s = $('#statusGambar'),
                        p = $('#prompt').value.trim();
                    if (!p) {
                        s.className = 'status bad';
                        s.textContent = T.needPrompt;
                        return;
                    }
                    s.className = 'status';
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
                            const img = document.createElement('img');
                            img.alt = p;
                            img.src = url;
                            const cap = document.createElement('figcaption');
                            const span = document.createElement('span');
                            span.textContent = p.slice(0, 70);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'ypdh-ai-' + i + '.png';
                            a.textContent = T.download;
                            cap.append(span, a);
                            f.append(img, cap);
                            $('#hasilGambar').prepend(f);
                        });
                        s.className = 'status ok';
                        s.textContent = t('drawn', {
                            n: (j.images || []).length
                        });
                    } catch (e) {
                        s.className = 'status bad';
                        s.textContent = t('error', {
                            message: e.message
                        });
                    } finally {
                        btnGambar.disabled = false;
                    }
                };
            }

            tulis.focus();
        })();
    </script>
</body>

</html>
