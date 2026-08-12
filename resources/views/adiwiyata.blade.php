<x-layouts.public :title="__('Monitoring Kelengkapan Dokumen Adiwiyata')">
    @push('styles')
        <style>
            #adiwiyata {
                --green-900: #14532d;
                --green-800: #166534;
                --green-700: #15803d;
                --green-600: #16a34a;
                --green-500: #22c55e;
                --green-100: #dcfce7;
                --green-50: #f0fdf4;
                --amber-700: #92400e;
                --amber-500: #f59e0b;
                --amber-100: #fef3c7;
                --red-700: #991b1b;
                --red-500: #ef4444;
                --red-100: #fee2e2;
                --gray-900: #1f2937;
                --gray-700: #374151;
                --gray-500: #6b7280;
                --gray-400: #9ca3af;
                --gray-300: #d1d5db;
                --gray-100: #f3f4f6;
                --surface: #fff;
                --radius: 12px;

                max-width: 960px;
                margin: 0 auto;
                color: var(--gray-900);
                line-height: 1.5;

                /* ponytail: CSS nesting keeps the ported stylesheet scoped to this page
                   instead of hand-prefixing ~60 selectors. */
                & * {
                    box-sizing: border-box;
                    margin: 0;
                    padding: 0;
                }

                /* Header */
                .ad-header {
                    display: flex;
                    align-items: center;
                    gap: 14px;
                    margin-bottom: 20px;
                }

                .logo {
                    width: 52px;
                    height: 52px;
                    border-radius: 14px;
                    background: linear-gradient(135deg, #16a34a, #166534);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 26px;
                    color: #fff;
                    box-shadow: 0 4px 12px rgba(22, 101, 52, .3);
                    flex-shrink: 0;
                }

                .ad-header h1 {
                    font-size: 20px;
                    font-weight: 800;
                    color: var(--green-900);
                    letter-spacing: -.3px;
                }

                .ad-header .sub {
                    font-size: 13px;
                    color: var(--gray-500);
                    margin-top: 2px;
                }

                .ad-header .sub b {
                    color: var(--gray-700);
                    font-weight: 600;
                }

                /* Cards ringkasan */
                .cards {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 12px;
                    margin-bottom: 18px;
                }

                .card {
                    background: var(--surface);
                    border: 1px solid var(--gray-100);
                    border-radius: var(--radius);
                    padding: 14px 16px;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
                }

                .card .num {
                    font-size: 26px;
                    font-weight: 800;
                    letter-spacing: -.5px;
                }

                .card .lbl {
                    font-size: 12px;
                    color: var(--gray-500);
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: .4px;
                    margin-top: 2px;
                }

                .card.ok .num {
                    color: var(--green-600);
                }

                .card.partial .num {
                    color: var(--amber-500);
                }

                .card.empty .num {
                    color: var(--red-500);
                }

                .card.total .num {
                    color: var(--green-900);
                }

                /* Progress */
                .progress-wrap {
                    background: var(--surface);
                    border: 1px solid var(--gray-100);
                    border-radius: var(--radius);
                    padding: 18px;
                    margin-bottom: 20px;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
                }

                .progress-head {
                    display: flex;
                    justify-content: space-between;
                    align-items: baseline;
                    margin-bottom: 10px;
                    flex-wrap: wrap;
                    gap: 6px;
                }

                .progress-head .pt {
                    font-size: 14px;
                    font-weight: 700;
                }

                .progress-head .pp {
                    font-size: 22px;
                    font-weight: 800;
                    color: var(--green-700);
                }

                .bar {
                    height: 14px;
                    background: var(--gray-100);
                    border-radius: 99px;
                    overflow: hidden;
                }

                .bar>div {
                    height: 100%;
                    background: linear-gradient(90deg, var(--green-600), var(--green-500));
                    border-radius: 99px;
                    transition: width .6s ease;
                }

                .progress-note {
                    font-size: 12px;
                    color: var(--gray-500);
                    margin-top: 8px;
                }

                .root-link {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    margin-top: 12px;
                    padding: 8px 14px;
                    border-radius: 99px;
                    background: var(--green-700);
                    color: #fff;
                    font-size: 13px;
                    font-weight: 700;
                    text-decoration: none;
                }

                .root-link:hover {
                    background: var(--green-800);
                    color: #fff;
                }

                /* Kontrol */
                .lockbar {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    flex-wrap: wrap;
                    background: var(--surface);
                    border: 1px solid var(--gray-300);
                    border-radius: var(--radius);
                    padding: 12px 14px;
                    margin-bottom: 14px;
                    font-size: 13px;
                    font-weight: 600;
                    color: var(--gray-700);
                }

                .lockbar.open {
                    background: var(--green-50);
                    border-color: var(--green-100);
                    color: var(--green-800);
                }

                .lockbar span {
                    flex: 1;
                    min-width: 180px;
                }

                .lockbar form {
                    display: contents;
                }

                .pin {
                    width: 120px;
                    padding: 8px 12px;
                    border: 1px solid var(--gray-300);
                    border-radius: 99px;
                    font-family: inherit;
                    font-size: 14px;
                    letter-spacing: 3px;
                    background: var(--surface);
                    color: var(--gray-900);
                }

                .pin:focus {
                    outline: none;
                    border-color: var(--green-600);
                    box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
                }

                .lockerr {
                    flex-basis: 100%;
                    color: var(--red-700);
                    font-size: 12px;
                }

                .rad.locked {
                    cursor: not-allowed;
                    opacity: .75;
                }

                .controls {
                    display: flex;
                    gap: 8px;
                    flex-wrap: wrap;
                    align-items: center;
                    margin-bottom: 14px;
                }

                .search {
                    flex: 1;
                    min-width: 200px;
                    padding: 9px 14px;
                    border: 1px solid var(--gray-300);
                    border-radius: 99px;
                    font-size: 14px;
                    font-family: inherit;
                    outline: none;
                    background: var(--surface);
                    color: var(--gray-900);
                }

                .search:focus {
                    border-color: var(--green-600);
                    box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
                }

                .chip {
                    padding: 7px 14px;
                    border-radius: 99px;
                    border: 1px solid var(--gray-300);
                    background: var(--surface);
                    font-size: 13px;
                    font-family: inherit;
                    font-weight: 600;
                    cursor: pointer;
                    color: var(--gray-700);
                    transition: all .15s;
                }

                .chip:hover {
                    border-color: var(--green-600);
                }

                .chip.active {
                    background: var(--green-700);
                    border-color: var(--green-700);
                    color: #fff;
                }

                /* Tree */
                .tree {
                    background: var(--surface);
                    border: 1px solid var(--gray-100);
                    border-radius: var(--radius);
                    overflow: hidden;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
                }

                .node {
                    border-bottom: 1px solid var(--gray-100);
                }

                .node:last-child {
                    border-bottom: none;
                }

                .node-row {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 9px 14px;
                    cursor: pointer;
                    transition: background .12s;
                }

                .node-row:hover {
                    background: var(--green-50);
                }

                .caret {
                    width: 18px;
                    height: 18px;
                    flex-shrink: 0;
                    color: var(--gray-500);
                    transition: transform .15s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 11px;
                }

                .caret.open {
                    transform: rotate(90deg);
                }

                .caret.leaf {
                    visibility: hidden;
                }

                .fname {
                    flex: 1;
                    font-size: 14px;
                    font-weight: 600;
                    color: var(--gray-900);
                    word-break: break-word;
                }

                .node-row.has-children .fname {
                    font-weight: 700;
                }

                .badge {
                    flex-shrink: 0;
                    font-size: 11px;
                    font-weight: 700;
                    padding: 3px 9px;
                    border-radius: 99px;
                    color: #fff;
                }

                .badge.ok {
                    background: var(--green-600);
                }

                .badge.partial {
                    background: var(--amber-500);
                }

                .badge.empty {
                    background: var(--red-500);
                }

                .cnt {
                    flex-shrink: 0;
                    font-size: 12px;
                    color: var(--gray-500);
                    font-weight: 600;
                    white-space: nowrap;
                }

                .mark {
                    flex-shrink: 0;
                    font-size: 12px;
                    line-height: 1;
                }

                .drive-ico {
                    flex-shrink: 0;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 8px;
                    font-size: 15px;
                    text-decoration: none;
                    color: var(--gray-500);
                    border: 1px solid transparent;
                }

                .drive-ico:hover {
                    background: var(--surface);
                    border-color: var(--green-600);
                }

                .drive-ico:focus-visible {
                    outline: 2px solid var(--green-600);
                    outline-offset: 1px;
                }

                .drive-ico.approx {
                    opacity: .42;
                }

                .btn-drive.approx {
                    border-style: dashed;
                    color: var(--gray-500);
                }

                .children {
                    display: none;
                    padding-left: 18px;
                    border-left: 2px solid var(--gray-100);
                    margin-left: 26px;
                }

                .children.open {
                    display: block;
                }

                /* Panel penilaian */
                .editor {
                    background: var(--green-50);
                    border: 1px solid var(--green-100);
                    border-radius: 10px;
                    padding: 14px;
                    margin: 8px 14px 10px 0;
                }

                .ed-label {
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: .4px;
                    color: var(--gray-500);
                    margin-bottom: 8px;
                }

                .radios {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(118px, 1fr));
                    gap: 8px;
                    margin-bottom: 14px;
                }

                .rad {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 5px;
                    padding: 11px 8px;
                    background: var(--surface);
                    border: 2px solid var(--gray-300);
                    border-radius: 10px;
                    font-size: 13px;
                    font-weight: 700;
                    color: var(--gray-700);
                    cursor: pointer;
                    user-select: none;
                    transition: border-color .15s, background .15s, color .15s;
                    text-align: center;
                }

                .rad input {
                    position: absolute;
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .rad:hover {
                    border-color: var(--gray-500);
                }

                .rad:focus-within {
                    outline: 3px solid rgba(22, 163, 74, .28);
                    outline-offset: 2px;
                }

                .rad.empty.checked {
                    background: var(--red-100);
                    border-color: var(--red-500);
                    color: var(--red-700);
                }

                .rad.partial.checked {
                    background: var(--amber-100);
                    border-color: var(--amber-500);
                    color: var(--amber-700);
                }

                .rad.ok.checked {
                    background: var(--green-100);
                    border-color: var(--green-600);
                    color: var(--green-800);
                }

                .note {
                    width: 100%;
                    min-height: 66px;
                    padding: 10px 12px;
                    border: 1px solid var(--gray-300);
                    border-radius: 10px;
                    font-family: inherit;
                    font-size: 13px;
                    line-height: 1.5;
                    resize: vertical;
                    background: var(--surface);
                    color: var(--gray-900);
                }

                .note:focus {
                    outline: none;
                    border-color: var(--green-600);
                    box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
                }

                .ed-actions {
                    display: flex;
                    gap: 8px;
                    align-items: center;
                    flex-wrap: wrap;
                    margin-top: 10px;
                }

                .btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 9px 16px;
                    border-radius: 99px;
                    font-size: 13px;
                    font-weight: 700;
                    border: none;
                    cursor: pointer;
                    text-decoration: none;
                    font-family: inherit;
                    transition: all .15s;
                }

                .btn:focus-visible {
                    outline: 2px solid var(--green-700);
                    outline-offset: 2px;
                }

                .btn-save {
                    background: var(--green-700);
                    color: #fff;
                }

                .btn-save:hover {
                    background: var(--green-800);
                }

                .btn-drive {
                    background: var(--surface);
                    border: 1px solid var(--gray-300);
                    color: var(--gray-700);
                }

                .btn-drive:hover {
                    border-color: var(--green-600);
                    color: var(--green-800);
                }

                .btn-ghost {
                    background: var(--surface);
                    border: 1px solid var(--gray-300);
                    color: var(--gray-700);
                    font-size: 12px;
                    padding: 7px 14px;
                }

                .btn-ghost:hover {
                    border-color: var(--green-600);
                }

                .state {
                    font-size: 11px;
                    color: var(--gray-500);
                    font-weight: 600;
                    margin-left: auto;
                    text-align: right;
                }

                .state.dirty {
                    color: var(--amber-500);
                }

                /* Files di dalam folder */
                .file {
                    padding: 6px 14px 6px 8px;
                    font-size: 13px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    border-top: 1px dashed var(--gray-100);
                }

                .file .dot {
                    font-size: 14px;
                    flex-shrink: 0;
                }

                .file .ftag {
                    font-size: 10px;
                    font-weight: 700;
                    padding: 1px 7px;
                    border-radius: 99px;
                    margin-left: auto;
                    flex-shrink: 0;
                    background: var(--green-100);
                    color: var(--green-800);
                }

                .none {
                    padding: 8px 14px;
                    font-size: 12px;
                    color: var(--gray-400);
                }

                .empty-msg {
                    display: none;
                    padding: 30px;
                    text-align: center;
                    color: var(--gray-500);
                    font-size: 14px;
                }

                .empty-msg.show {
                    display: block;
                }

                .ad-footer {
                    margin-top: 18px;
                    font-size: 12px;
                    color: var(--gray-500);
                    text-align: center;
                }

                .stamp {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    background: var(--green-50);
                    border: 1px solid var(--green-100);
                    color: var(--green-800);
                    padding: 4px 12px;
                    border-radius: 99px;
                    font-weight: 600;
                }

                .foot-actions {
                    display: flex;
                    gap: 8px;
                    justify-content: center;
                    flex-wrap: wrap;
                    margin-top: 12px;
                }
            }

            #adiwiyata-toast {
                position: fixed;
                left: 50%;
                bottom: 24px;
                transform: translateX(-50%) translateY(20px);
                background: #14532d;
                color: #fff;
                padding: 11px 22px;
                border-radius: 99px;
                font-size: 13px;
                font-weight: 700;
                opacity: 0;
                pointer-events: none;
                transition: opacity .25s, transform .25s;
                z-index: 60;
                box-shadow: 0 6px 20px rgba(0, 0, 0, .2);
            }

            #adiwiyata-toast.show {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }

            html.dark #adiwiyata {
                --surface: #0f172a;
                --gray-900: #e2e8f0;
                --gray-700: #cbd5e1;
                --gray-500: #94a3b8;
                --gray-400: #64748b;
                --gray-300: #334155;
                --gray-100: #1e293b;
                --green-900: #bbf7d0;
                --green-800: #86efac;
                --green-100: #1c3a29;
                --green-50: #132a1e;
                --red-100: #431a1a;
                --red-700: #fca5a5;
                --amber-100: #3b2c0c;
                --amber-700: #fcd34d;
            }

            html.dark #adiwiyata .progress-head .pp {
                color: var(--green-500);
            }

            @media(max-width:560px) {
                #adiwiyata .ad-header h1 {
                    font-size: 17px;
                }

                #adiwiyata .cards {
                    grid-template-columns: 1fr 1fr;
                }

                #adiwiyata .cnt {
                    display: none;
                }

                #adiwiyata .editor {
                    margin-right: 8px;
                }
            }

            @media(prefers-reduced-motion:reduce) {
                #adiwiyata * {
                    transition: none !important;
                    animation: none !important;
                }
            }
        </style>
    @endpush

    <div id="adiwiyata">
        <div class="ad-header">
            <div class="logo">🌿</div>
            <div>
                <h1>{{ __('Monitoring Kelengkapan Dokumen Adiwiyata') }}</h1>
                <div class="sub">{{ __('Folder') }}: <b id="folderName">-</b></div>
            </div>
        </div>

        <div class="progress-wrap">
            <div class="progress-head">
                <span class="pt">{{ __('Progres Kelengkapan Folder') }}</span>
                <span class="pp" id="pct">0%</span>
            </div>
            <div class="bar">
                <div id="barFill" style="width:0%"></div>
            </div>
            <div class="progress-note" id="progressNote">-</div>
            <a class="root-link" id="rootLink" target="_blank" rel="noopener">📂
                {{ __('Buka folder utama di Google Drive') }}</a>
        </div>

        <div class="cards">
            <div class="card total">
                <div class="num" id="cTotal">0</div>
                <div class="lbl">{{ __('Total Folder') }}</div>
            </div>
            <div class="card ok">
                <div class="num" id="cOk">0</div>
                <div class="lbl">🟢 {{ __('Lengkap') }}</div>
            </div>
            <div class="card partial">
                <div class="num" id="cPartial">0</div>
                <div class="lbl">🟡 {{ __('Belum Lengkap') }}</div>
            </div>
            <div class="card empty">
                <div class="num" id="cEmpty">0</div>
                <div class="lbl">🔴 {{ __('Kosong') }}</div>
            </div>
        </div>

        @if ($canSave)
            <div class="lockbar open">
                <span>🔓 {{ __('Mode ubah aktif. Penilaian tersimpan di server dan terlihat oleh semua orang.') }}</span>
                <form method="POST" action="{{ route('adiwiyata.lock') }}">
                    @csrf
                    <button type="submit" class="btn btn-ghost">{{ __('Kunci lagi') }}</button>
                </form>
            </div>
        @elseif ($pinConfigured)
            <form method="POST" action="{{ route('adiwiyata.unlock') }}" class="lockbar">
                @csrf
                <span>🔒 {{ __('Masukkan PIN untuk mengubah penilaian') }}</span>
                <input type="password" name="pin" class="pin" autocomplete="off" inputmode="numeric"
                    aria-label="{{ __('PIN') }}" required>
                <button type="submit" class="btn btn-save">{{ __('Buka') }}</button>
                @error('pin')
                    <span class="lockerr">{{ $message }}</span>
                @enderror
            </form>
        @else
            <div class="lockbar">
                <span>🔒 {{ __('Halaman ini baca-saja. Penyimpanan penilaian belum diaktifkan.') }}</span>
            </div>
        @endif

        <div class="controls">
            <input class="search" id="adSearch" type="search"
                placeholder="🔍 {{ __('Cari folder / kriteria...') }}" aria-label="{{ __('Cari folder / kriteria...') }}">
            <button type="button" class="chip active" data-f="all">{{ __('Semua') }}</button>
            <button type="button" class="chip" data-f="incomplete">{{ __('Belum Selesai') }}</button>
            <button type="button" class="chip" data-f="ok">{{ __('Lengkap') }}</button>
            <button type="button" class="chip" data-f="partial">{{ __('Belum Lengkap') }}</button>
            <button type="button" class="chip" data-f="empty">{{ __('Kosong') }}</button>
            <button type="button" class="chip" data-f="noted">{{ __('Ada Catatan') }}</button>
        </div>

        <div class="tree" id="tree"></div>
        <div class="empty-msg" id="emptyMsg">{{ __('Tidak ada folder yang cocok dengan filter 😊') }}</div>

        <div class="ad-footer">
            <span class="stamp">🔄 {{ __('Terakhir dipindai') }}: <span id="updated">-</span></span>
            <div class="foot-actions">
                <button type="button" class="btn btn-ghost" id="btnExport">⬇️
                    {{ __('Unduh hasil penilaian (JSON)') }}</button>
                @if ($canSave)
                    <button type="button" class="btn btn-ghost" id="btnReset">🗑️
                        {{ __('Kosongkan penilaian') }}</button>
                @endif
            </div>
            <div style="margin-top:10px">{{ __('Sistem Monitoring Bukti Kegiatan PBLHS') }} ·
                {{ __('tersimpan di server, sama untuk semua perangkat') }}</div>
            <div style="margin-top:4px" id="linkStat">-</div>
        </div>
    </div>

    <div id="adiwiyata-toast" role="status" aria-live="polite"></div>

    @push('scripts')
        <script>
            (function() {
                const DATA_URL = @json(route('adiwiyata.data'));
                const SAVE_URL = @json(route('adiwiyata.save'));
                const RESET_URL = @json(route('adiwiyata.reset'));
                const CAN_SAVE = @json($canSave);
                const CSRF = document.querySelector('meta[name="csrf-token"]').content;
                const LOCALE = @json(str_replace('_', '-', app()->getLocale()));

                /* Teks dinamis diterjemahkan di server supaya halaman ini ikut dwibahasa. */
                @php
                    $strings = [
                        'ok' => __('Lengkap'),
                        'partial' => __('Belum Lengkap'),
                        'empty' => __('Kosong'),
                        'docs' => __(':n dokumen'),
                        'noSubfolder' => __('— tidak ada subfolder yang cocok —'),
                        'emptyFolder' => __('— folder masih kosong di Drive —'),
                        'driveExact' => __('Buka folder ini di Google Drive'),
                        'driveParent' => __('ID folder ini belum ada — dibuka ke folder induk terdekat di Drive'),
                        'driveSearch' => __('ID folder belum ada — dibuka ke pencarian Drive'),
                        'openDrive' => __('Buka folder di Google Drive'),
                        'openDriveParent' => __('Buka folder induk di Drive'),
                        'statusLabel' => __('Status folder ini'),
                        'noteLabel' => __('Catatan — apa yang belum ada?'),
                        'notePlaceholder' => __('Contoh: daftar hadir dan notulensi belum diunggah'),
                        'save' => __('Simpan'),
                        'unsaved' => __('• belum disimpan'),
                        'savedAt' => __('Disimpan :time'),
                        'fromScan' => __('Dari hasil pemindaian'),
                        'markManual' => __('Diisi manual'),
                        'markNote' => __('Ada catatan'),
                        'tagDoc' => __('DOKUMEN'),
                        'progressNote' => __(':ok dari :tot folder sudah lengkap · :partial belum lengkap · :empty masih kosong · :manual sudah dinilai guru · :noted punya catatan'),
                        'linkAll' => __('🔗 Semua :n link folder sudah tepat sasaran'),
                        'linkSome' => __('🔗 :exact dari :n link folder tepat sasaran — sisanya membuka folder induk terdekat'),
                        'savedToast' => __('Disimpan — :status'),
                        'exported' => __('Diunduh — :n folder'),
                        'nothingToExport' => __('Belum ada penilaian untuk diunduh'),
                        'noAssessment' => __('Belum ada penilaian'),
                        'confirmReset' => __('Kosongkan :n penilaian? Status akan kembali ke hasil pemindaian Drive.'),
                        'resetDone' => __('Penilaian dikosongkan'),
                        'locked' => __('Sesi PIN sudah habis. Muat ulang halaman dan masukkan PIN lagi.'),
                        'saveFailed' => __('Gagal menyimpan: :error'),
                        'loadFailed' => __('Gagal memuat data pemindaian.'),
                    ];
                @endphp
                const T = @json($strings);

                function t(key, vars) {
                    let s = T[key] || key;
                    for (const k in (vars || {})) s = s.replaceAll(':' + k, vars[k]);
                    return s;
                }

                const STATUS = {
                    ok: [T.ok, "ok"],
                    partial: [T.partial, "partial"],
                    empty: [T.empty, "empty"]
                };
                const OPTIONS = [
                    ["empty", "🔴 " + T.empty],
                    ["partial", "🟡 " + T.partial],
                    ["ok", "🟢 " + T.ok]
                ];

                function esc(s) {
                    return String(s).replace(/[&<>"']/g, c => ({
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': "&quot;",
                        "'": "&#39;"
                    } [c]))
                }

                let data = null,
                    filter = "all",
                    q = "";
                /* Penilaian datang dari server, jadi sama untuk semua browser dan perangkat. */
                let overrides = @json($assessments); // {key:{status,note,savedAt}}
                let drafts = {}; // {key:{status,note}} -> belum disimpan
                const byKey = {};
                const openKeys = new Set();

                function post(url, body) {
                    return fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": CSRF
                        },
                        body: JSON.stringify(body || {})
                    }).then(async res => {
                        if (!res.ok) throw new Error(res.status === 403 ? T.locked : "HTTP " + res.status);
                        return res.json();
                    });
                }

                /* ---------- Persiapan pohon ---------- */
                function prepare(n, parents, inheritedId) {
                    n._path = parents.concat([n.n]);
                    n._key = n._path.join(" / ");
                    n._dirs = (n.c || []).filter(c => c.t === "d");
                    /* Hanya dokumen nyata yang ditampilkan — berkas panduan/placeholder
                       hasil generator template diabaikan. */
                    n._files = (n.c || []).filter(c => c.t === "f" && c.k === "real");
                    n._isLeaf = n._dirs.length === 0;

                    /* Link Drive: pakai ID folder ini kalau ada. Kalau belum ada, warisi ID
                       folder induk terdekat supaya guru mendarat di lokasi yang benar,
                       bukan di halaman pencarian. */
                    n._exact = !!n.id;
                    n._linkId = n.id || inheritedId || null;

                    byKey[n._key] = n;
                    n._dirs.forEach(c => prepare(c, n._path, n._linkId));
                }

                function leaves(n) {
                    if (n._isLeaf) return [n];
                    return n._dirs.flatMap(leaves);
                }

                /* Status efektif: folder ujung pakai pilihan guru kalau sudah disimpan,
                   kalau belum pakai hasil pemindaian Drive. Folder induk diturunkan dari anaknya. */
                function effStatus(n) {
                    if (n._isLeaf) {
                        const o = overrides[n._key];
                        return (o && o.status) || n.st;
                    }
                    const st = leaves(n).map(effStatus);
                    if (!st.length) return n.st;
                    if (st.every(s => s === "ok")) return "ok";
                    if (st.every(s => s === "empty")) return "empty";
                    return "partial";
                }

                function hasNote(n) {
                    const o = overrides[n._key];
                    return !!(o && o.note && o.note.trim());
                }

                function isManual(n) {
                    return n._isLeaf && !!overrides[n._key];
                }

                function driveUrl(n) {
                    if (n._linkId) return "https://drive.google.com/drive/folders/" + n._linkId;
                    return "https://drive.google.com/drive/search?q=" + encodeURIComponent(n.n);
                }

                function driveTitle(n) {
                    if (n._exact) return T.driveExact;
                    if (n._linkId) return T.driveParent;
                    return T.driveSearch;
                }

                /* ---------- Filter & pencarian ---------- */
                function matchesFilter(n) {
                    if (filter === "all") return true;
                    if (filter === "incomplete") return effStatus(n) !== "ok";
                    if (filter === "noted") return hasNote(n);
                    return effStatus(n) === filter;
                }

                function matchesSearch(n) {
                    return !q || n.n.toLowerCase().includes(q);
                }

                function visible(n) {
                    return (matchesFilter(n) && matchesSearch(n)) || n._dirs.some(visible);
                }

                /* ---------- Render ---------- */
                function fileRow(f) {
                    return `<div class="file">
                        <span class="dot">✅</span>${esc(f.n)}
                        <span class="ftag">${esc(T.tagDoc)}</span></div>`;
                }

                function editorHtml(n) {
                    const saved = overrides[n._key] || null;
                    const draft = drafts[n._key] || null;
                    const cur = (draft && draft.status) || (saved && saved.status) || n.st;
                    const note = (draft && draft.note !== undefined) ? draft.note : ((saved && saved.note) || "");
                    const grp = "st_" + Math.abs(hash(n._key));
                    const k = esc(n._key);

                    /* Terkunci: status & catatan tetap terlihat, tapi tidak bisa diubah.
                       Server tetap menolak simpan tanpa session PIN — ini cuma lapisan UI. */
                    const off = CAN_SAVE ? "" : " disabled";

                    const radios = OPTIONS.map(([v, label]) =>
                        `<label class="rad ${v}${cur===v?" checked":""}${CAN_SAVE?"":" locked"}">
                           <input type="radio" name="${grp}" value="${v}" data-key="${k}"${cur===v?" checked":""}${off}>
                           <span>${esc(label)}</span>
                         </label>`).join("");

                    let state;
                    if (draft) state = `<span class="state dirty" data-state="${k}">${esc(T.unsaved)}</span>`;
                    else if (saved) state =
                        `<span class="state" data-state="${k}">${esc(t("savedAt",{time:fmt(saved.savedAt)}))}</span>`;
                    else state = `<span class="state" data-state="${k}">${esc(T.fromScan)}</span>`;

                    return `<div class="editor">
                        <div class="ed-label">${esc(T.statusLabel)}</div>
                        <div class="radios">${radios}</div>
                        <div class="ed-label">${esc(T.noteLabel)}</div>
                        <textarea class="note" data-key="${k}" placeholder="${esc(T.notePlaceholder)}"${off}>${esc(note)}</textarea>
                        <div class="ed-actions">
                          <a class="btn btn-drive${n._exact?"":" approx"}" href="${esc(driveUrl(n))}" target="_blank" rel="noopener" title="${esc(driveTitle(n))}">📂 ${esc(n._exact?T.openDrive:T.openDriveParent)}</a>
                          ${CAN_SAVE?`<button type="button" class="btn btn-save" data-key="${k}">💾 ${esc(T.save)}</button>`:""}
                          ${state}
                        </div>
                      </div>`;
                }

                function renderNode(n) {
                    const st = STATUS[effStatus(n)] || ["?", "empty"];
                    const kids = n._dirs.filter(visible);
                    const hasKids = n._dirs.length > 0;
                    const cnt = hasKids ?
                        `${kids.length}/${n._dirs.length}` :
                        esc(t("docs", {
                            n: n._files.length
                        }));
                    const marks = (isManual(n) ? `<span class="mark" title="${esc(T.markManual)}">✍️</span>` : "") +
                        (hasNote(n) ? `<span class="mark" title="${esc(T.markNote)}">📝</span>` : "");

                    const row = `<div class="node-row${hasKids?" has-children":""}">
                          <span class="caret ${hasKids?"":"leaf"}${openKeys.has(n._key)?" open":""}">▶</span>
                          <span class="fname">📁 ${esc(n.n)}</span>
                          ${marks}
                          <span class="cnt">${cnt}</span>
                          <a class="drive-ico${n._exact?"":" approx"}" href="${esc(driveUrl(n))}" target="_blank" rel="noopener" title="${esc(driveTitle(n))}">📂</a>
                          <span class="badge ${st[1]}">${esc(st[0])}</span>
                        </div>`;

                    let inner;
                    if (hasKids) {
                        inner = kids.length ? kids.map(renderNode).join("") :
                            `<div class="none">${esc(T.noSubfolder)}</div>`;
                    } else {
                        inner = editorHtml(n) +
                            (n._files.length ? n._files.map(fileRow).join("") :
                                `<div class="none">${esc(T.emptyFolder)}</div>`);
                    }

                    return `<div class="node" data-key="${esc(n._key)}">${row}
                        <div class="children${openKeys.has(n._key)?" open":""}">${inner}</div></div>`;
                }

                function stats() {
                    const L = leaves(data.root);
                    const c = {
                        tot: L.length,
                        ok: 0,
                        partial: 0,
                        empty: 0,
                        manual: 0,
                        noted: 0
                    };
                    L.forEach(n => {
                        c[effStatus(n)] = (c[effStatus(n)] || 0) + 1;
                        if (isManual(n)) c.manual++;
                        if (hasNote(n)) c.noted++;
                    });
                    return c;
                }

                function render() {
                    if (!data) return;
                    const root = data.root;
                    document.getElementById("folderName").textContent = root.n;
                    document.getElementById("updated").textContent = data.generated;
                    document.getElementById("rootLink").href = driveUrl(root);

                    const all = Object.values(byKey),
                        exact = all.filter(n => n._exact).length;
                    document.getElementById("linkStat").textContent = exact === all.length ?
                        t("linkAll", {
                            n: all.length
                        }) :
                        t("linkSome", {
                            exact: exact,
                            n: all.length
                        });

                    const a = stats();
                    const pct = a.tot ? Math.round(a.ok / a.tot * 100) : 0;
                    document.getElementById("pct").textContent = pct + "%";
                    document.getElementById("barFill").style.width = pct + "%";
                    document.getElementById("progressNote").textContent = t("progressNote", a);
                    document.getElementById("cTotal").textContent = a.tot;
                    document.getElementById("cOk").textContent = a.ok;
                    document.getElementById("cPartial").textContent = a.partial;
                    document.getElementById("cEmpty").textContent = a.empty;

                    const kids = root._dirs.filter(visible);
                    document.getElementById("tree").innerHTML = kids.map(renderNode).join("");
                    document.getElementById("emptyMsg").classList.toggle("show", kids.length === 0);
                }

                /* ---------- Aksi ---------- */
                async function saveOne(key) {
                    const node = byKey[key];
                    const draft = drafts[key] || {};
                    const prev = overrides[key] || {};
                    const status = draft.status || prev.status || (node ? node.st : "empty");
                    const note = draft.note !== undefined ? draft.note : (prev.note || "");

                    try {
                        // Server yang menentukan hasil akhir — jangan optimistis, kalau
                        // gagal draft tetap utuh supaya isian guru tidak hilang.
                        overrides[key] = await post(SAVE_URL, {
                            folder_key: key,
                            status,
                            note
                        });
                        delete drafts[key];
                    } catch (e) {
                        toast(t("saveFailed", {
                            error: e.message
                        }));
                        return;
                    }

                    const y = window.scrollY;
                    render();
                    window.scrollTo(0, y);
                    toast(t("savedToast", {
                        status: STATUS[status][0]
                    }));
                }

                function markState(key) {
                    const el = document.querySelector(`[data-state="${cssEsc(key)}"]`);
                    if (el) {
                        el.textContent = T.unsaved;
                        el.classList.add("dirty");
                    }
                }

                function toast(msg) {
                    const el = document.getElementById("adiwiyata-toast");
                    el.textContent = msg;
                    el.classList.add("show");
                    clearTimeout(toast._t);
                    toast._t = setTimeout(() => el.classList.remove("show"), 2200);
                }

                function exportJson() {
                    const items = Object.entries(overrides).map(([folder, v]) => ({
                        folder,
                        status: v.status,
                        label: STATUS[v.status][0],
                        catatan: v.note || "",
                        disimpan: v.savedAt
                    }));
                    const payload = {
                        dipindai: data.generated,
                        diekspor: new Date().toISOString(),
                        jumlah: items.length,
                        items
                    };
                    const url = URL.createObjectURL(new Blob([JSON.stringify(payload, null, 2)], {
                        type: "application/json"
                    }));
                    const a = document.createElement("a");
                    a.href = url;
                    a.download = "status-adiwiyata.json";
                    a.click();
                    URL.revokeObjectURL(url);
                    toast(items.length ? t("exported", {
                        n: items.length
                    }) : T.nothingToExport);
                }

                function hash(s) {
                    let h = 0;
                    for (let i = 0; i < s.length; i++) {
                        h = (h * 31 + s.charCodeAt(i)) | 0;
                    }
                    return h;
                }

                function cssEsc(s) {
                    return s.replace(/["\\]/g, m => "\\" + m);
                }

                function fmt(iso) {
                    try {
                        return new Date(iso).toLocaleString(LOCALE, {
                            day: "2-digit",
                            month: "short",
                            hour: "2-digit",
                            minute: "2-digit"
                        });
                    } catch (e) {
                        return "";
                    }
                }

                /* ---------- Event ---------- */
                const tree = document.getElementById("tree");

                tree.addEventListener("click", e => {
                    if (e.target.closest(".drive-ico") || e.target.closest(".btn-drive"))
                        return; // biarkan link terbuka

                    const save = e.target.closest(".btn-save");
                    if (save) {
                        saveOne(save.dataset.key);
                        return;
                    }

                    if (e.target.closest(".editor")) return; // klik di dalam panel tidak menutup folder

                    const row = e.target.closest(".node-row");
                    if (!row) return;
                    const node = row.closest(".node");
                    const key = node.dataset.key;
                    const kids = row.nextElementSibling;
                    if (!kids) return;
                    const open = kids.classList.toggle("open");
                    row.querySelector(".caret").classList.toggle("open", open);
                    open ? openKeys.add(key) : openKeys.delete(key);
                });

                tree.addEventListener("change", e => {
                    const r = e.target.closest('input[type="radio"]');
                    if (!r) return;
                    const key = r.dataset.key;
                    drafts[key] = Object.assign({}, overrides[key], drafts[key], {
                        status: r.value
                    });
                    r.closest(".radios").querySelectorAll(".rad").forEach(l => l.classList.remove("checked"));
                    r.closest(".rad").classList.add("checked");
                    markState(key);
                });

                tree.addEventListener("input", e => {
                    const ta = e.target.closest(".note");
                    if (!ta) return;
                    const key = ta.dataset.key;
                    drafts[key] = Object.assign({}, overrides[key], drafts[key], {
                        note: ta.value
                    });
                    markState(key);
                });

                document.querySelectorAll("#adiwiyata .chip").forEach(ch => {
                    ch.addEventListener("click", () => {
                        document.querySelectorAll("#adiwiyata .chip").forEach(x => x.classList.remove(
                            "active"));
                        ch.classList.add("active");
                        filter = ch.dataset.f;
                        render();
                    });
                });

                document.getElementById("adSearch").addEventListener("input", e => {
                    q = e.target.value.trim().toLowerCase();
                    render();
                });

                document.getElementById("btnExport").addEventListener("click", exportJson);

                const btnReset = document.getElementById("btnReset");
                if (btnReset) {
                    btnReset.addEventListener("click", async () => {
                        const n = Object.keys(overrides).length;
                        if (!n) {
                            toast(T.noAssessment);
                            return;
                        }
                        if (!confirm(t("confirmReset", {
                                n
                            }))) return;
                        try {
                            await post(RESET_URL);
                        } catch (e) {
                            toast(t("saveFailed", {
                                error: e.message
                            }));
                            return;
                        }
                        overrides = {};
                        drafts = {};
                        render();
                        toast(T.resetDone);
                    });
                }

                /* ---------- Mulai ---------- */
                (async function init() {
                    try {
                        const res = await fetch(DATA_URL);
                        if (!res.ok) throw new Error(res.status);
                        data = await res.json();
                    } catch (e) {
                        document.getElementById("tree").innerHTML =
                            `<div class="none">${esc(T.loadFailed)}</div>`;
                        return;
                    }
                    prepare(data.root, [], null);
                    render();
                })();
            })();
        </script>
    @endpush
</x-layouts.public>
