<form wire:submit="save" class="space-y-6">
    {{-- Branding --}}
    <div class="card">
        <h2 class="font-bold text-slate-900 mb-1">Branding & Logo</h2>
        <p class="text-xs text-slate-500 mb-4">Logo Aplikasi tampil di panel admin/guru. Logo Sekolah tampil di header & footer publik. Favicon tampil di tab browser.</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ([
                ['key' => 'app_logo', 'prop' => 'app_logo_file', 'label' => 'Logo Aplikasi', 'hint' => 'PNG/JPG/SVG, maks 2MB. Disarankan rasio 1:1.', 'upload' => $app_logo_file],
                ['key' => 'school_logo', 'prop' => 'school_logo_file', 'label' => 'Logo Sekolah', 'hint' => 'PNG/JPG/SVG, maks 2MB.', 'upload' => $school_logo_file],
                ['key' => 'favicon', 'prop' => 'favicon_file', 'label' => 'Favicon', 'hint' => 'PNG/ICO 32×32 atau 64×64, maks 512KB.', 'upload' => $favicon_file],
                ['key' => 'hero_image', 'prop' => 'hero_image_file', 'label' => 'Gambar Hero (Beranda)', 'hint' => 'JPG/PNG, rasio 5:4 atau 4:3 (min 900px lebar), maks 4MB.', 'upload' => $hero_image_file],
            ] as $b)
                <div>
                    <label class="label">{{ $b['label'] }}</label>
                    <div class="rounded-xl border border-dashed border-slate-200 p-4 bg-slate-50/50">
                        <div class="flex items-center justify-center h-24 mb-3 bg-white rounded-lg ring-1 ring-slate-100 overflow-hidden">
                            @if ($b['upload'])
                                <img src="{{ $b['upload']->temporaryUrl() }}" alt="" class="max-h-20 max-w-full object-contain">
                            @elseif ($logos[$b['key']] ?? null)
                                <img src="{{ asset('storage/' . $logos[$b['key']]) }}" alt="" class="max-h-20 max-w-full object-contain">
                            @else
                                <span class="text-xs text-slate-400">Belum ada gambar</span>
                            @endif
                        </div>
                        <input type="file" wire:model="{{ $b['prop'] }}" accept="image/*" class="text-xs w-full">
                        <div wire:loading wire:target="{{ $b['prop'] }}" class="text-xs text-brand-600 mt-1">Mengunggah…</div>
                        @error($b['prop'])<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-500 mt-1">{{ $b['hint'] }}</p>
                        @if ($logos[$b['key']] ?? null)
                            <button type="button" wire:click="removeImage('{{ $b['key'] }}')" wire:confirm="Hapus gambar ini?" class="mt-2 text-xs text-red-600 hover:underline">Hapus</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Tema Warna --}}
    <div class="card">
        <h2 class="font-bold text-slate-900 mb-1">Tema Warna</h2>
        <p class="text-xs text-slate-500 mb-4">Pilih warna utama (brand). Sistem akan otomatis menghasilkan 11 shade (50–950) dan diterapkan ke seluruh tombol, link, dan aksen di situs maupun panel admin.</p>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Picker --}}
            <div>
                <label class="label">Warna Utama (shade 500)</label>
                <div class="flex items-center gap-3">
                    <input type="color" wire:model.live.debounce.200ms="brandColor"
                        class="h-14 w-14 rounded-xl border border-slate-200 cursor-pointer">
                    <input type="text" wire:model.live.debounce.300ms="brandColor"
                        class="input font-mono uppercase" maxlength="7" placeholder="#65ad36">
                </div>
                @error('brandColor')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror

                <div class="mt-5">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Preset</p>
                    <div class="grid grid-cols-6 gap-2">
                        @foreach ($presets as $key => $p)
                            <button type="button" wire:click="applyPreset('{{ $p['hex'] }}')"
                                class="aspect-square rounded-lg ring-2 ring-transparent hover:ring-slate-300 transition-all ease-ios duration-200 active:scale-90 {{ strtolower($brandColor) === strtolower($p['hex']) ? '!ring-slate-900' : '' }}"
                                style="background:{{ $p['hex'] }}"
                                title="{{ $p['name'] }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Generated palette preview --}}
            <div class="lg:col-span-2">
                <p class="text-xs font-semibold text-slate-700 mb-2">Generated Palette</p>
                <div class="grid grid-cols-11 gap-1 mb-4">
                    @foreach ($palette as $shade => $hex)
                        <div class="text-center">
                            <div class="aspect-square rounded-md ring-1 ring-slate-100 mb-1" style="background:{{ $hex }}"></div>
                            <div class="text-[10px] font-mono text-slate-500">{{ $shade }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Preview --}}
                <p class="text-xs font-semibold text-slate-700 mb-2">Preview</p>
                <div class="rounded-xl ring-1 ring-slate-100 p-4 flex flex-wrap items-center gap-3"
                    style="
                        --color-brand-50:{{ $palette[50] }};
                        --color-brand-100:{{ $palette[100] }};
                        --color-brand-500:{{ $palette[500] }};
                        --color-brand-600:{{ $palette[600] }};
                        --color-brand-700:{{ $palette[700] }};
                    ">
                    <button type="button" class="btn-primary">Tombol Utama →</button>
                    <button type="button" class="btn-secondary">Tombol Sekunder</button>
                    <span class="rounded-full bg-brand-100 text-brand-700 px-3 py-1 text-xs font-semibold">Badge</span>
                    <a href="#" class="text-brand-600 text-sm font-semibold hover:underline">Link contoh</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Field umum --}}
    @php
        $richKeys = ['visi', 'misi', 'about_history', 'footer_about', 'hero_subtitle'];
        $shortText = ['hero_subtitle', 'footer_about'];
    @endphp

    <div class="card">
        <h2 class="font-bold text-slate-900 mb-4">Konten & Identitas</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach ($fields as $key => $label)
                <div class="{{ in_array($key, $richKeys) ? 'sm:col-span-2' : '' }}">
                    <label class="label">{{ $label }}</label>
                    @if (in_array($key, $richKeys))
                        <x-rich-editor
                            model="data.{{ $key }}"
                            :value="$data[$key] ?? ''"
                            placeholder="Tulis {{ strtolower($label) }}…"
                        />
                    @else
                        <input wire:model="data.{{ $key }}" class="input">
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-2">
        <button class="btn-primary">
            <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
            <span wire:loading wire:target="save">Menyimpan…</span>
        </button>
    </div>
</form>
