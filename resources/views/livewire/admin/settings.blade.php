<div class="space-y-6">
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

    {{-- Tema Event --}}
    <div class="grid xl:grid-cols-5 gap-6">
        <div class="xl:col-span-3 card">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="font-bold text-slate-900 mb-1">Tema Event Hari Besar</h2>
                    <p class="text-xs text-slate-500">Tema aktif otomatis tampil di halaman public berdasarkan rentang tanggal dan prioritas.</p>
                </div>
                <input wire:model.live.debounce.300ms="eventSearch" placeholder="Cari event..." class="input sm:max-w-xs">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm responsive-table">
                    <thead class="text-left text-slate-500 border-b">
                        <tr>
                            <th class="py-2">Tema</th>
                            <th>Periode</th>
                            <th>Warna</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($eventThemes as $theme)
                            @php
                                $activeNow = $theme->isActiveOn(now());
                                $styleLabel = $eventPresets[$theme->style]['label'] ?? $theme->style;
                            @endphp
                            <tr>
                                <td data-label="Tema" class="py-3">
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $theme->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $styleLabel }} · Prioritas {{ $theme->priority }}</div>
                                        @if ($theme->message)
                                            <div class="text-xs text-slate-500 mt-1 line-clamp-1">{{ $theme->message }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Periode" class="text-slate-600">
                                    <div>{{ $theme->start_date->format('d/m/Y') }} - {{ $theme->end_date->format('d/m/Y') }}</div>
                                    @if ($theme->repeat_annually)
                                        <div class="text-[11px] text-brand-600 font-semibold">Berulang tiap tahun</div>
                                    @endif
                                </td>
                                <td data-label="Warna">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center gap-1.5">
                                            @foreach ([$theme->primary_color, $theme->secondary_color, $theme->accent_color] as $color)
                                                <span class="h-5 w-5 rounded-full ring-1 ring-slate-200" style="background: {{ $color }}"></span>
                                            @endforeach
                                        </div>
                                        @if ($theme->background_image)
                                            <span class="h-7 w-10 rounded-md bg-cover bg-center ring-1 ring-slate-200"
                                                style="background-image: url('{{ $theme->backgroundImageUrl() }}')"
                                                title="Ada background public"></span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Status">
                                    @if ($activeNow)
                                        <span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs font-semibold">Sedang tampil</span>
                                    @elseif ($theme->is_active)
                                        <span class="rounded-full bg-slate-100 text-slate-600 px-2 py-0.5 text-xs font-semibold">Terjadwal</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs font-semibold">Nonaktif</span>
                                    @endif
                                </td>
                                <td data-label="Aksi" class="text-right">
                                    <button type="button" wire:click="editEventTheme({{ $theme->id }})"
                                        class="text-brand-600 hover:underline text-xs">Edit</button>
                                    <button type="button"
                                        wire:click="confirmDelete({{ $theme->id }}, @js($theme->name))"
                                        class="text-red-600 hover:underline text-xs ml-2">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-500">Belum ada tema event.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="xl:col-span-2 card">
            <h2 class="font-bold text-slate-900 mb-3">{{ $eventEditingId ? 'Edit Tema Event' : 'Tambah Tema Event' }}</h2>
            <form wire:submit="saveEventTheme" class="space-y-4">
                <div>
                    <label class="label">Preset Cepat</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-2 gap-2">
                        @foreach ($eventPresets as $style => $preset)
                            <button type="button" wire:click="applyEventPreset('{{ $style }}')"
                                class="rounded-xl px-3 py-2 text-left text-xs font-semibold ring-1 transition {{ $eventStyle === $style ? 'ring-slate-900 bg-slate-50' : 'ring-slate-200 hover:bg-slate-50' }}">
                                <span class="inline-flex h-2.5 w-2.5 rounded-full mr-1.5" style="background: {{ $preset['primary_color'] }}"></span>
                                {{ $preset['label'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="label">Nama Tema</label>
                        <input wire:model="eventName" class="input" placeholder="Contoh: Hari Kemerdekaan RI">
                        @error('eventName')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Gaya Tampilan</label>
                        <select wire:model="eventStyle" class="select">
                            @foreach ($eventPresets as $style => $preset)
                                <option value="{{ $style }}">{{ $preset['label'] }}</option>
                            @endforeach
                        </select>
                        @error('eventStyle')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Prioritas</label>
                        <input type="number" wire:model="eventPriority" min="0" max="1000" class="input">
                        @error('eventPriority')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Tanggal Mulai</label>
                        <input type="date" wire:model="eventStartDate" class="input">
                        @error('eventStartDate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="label">Tanggal Selesai</label>
                        <input type="date" wire:model="eventEndDate" class="input">
                        @error('eventEndDate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Pesan Banner</label>
                        <input wire:model="eventMessage" class="input" maxlength="180" placeholder="Pesan singkat di halaman public">
                        @error('eventMessage')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="label">Warna Tema</label>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach ([
                            'eventPrimaryColor' => 'Utama',
                            'eventSecondaryColor' => 'Sekunder',
                            'eventAccentColor' => 'Aksen',
                            'eventBackgroundColor' => 'Latar',
                            'eventTextColor' => 'Teks',
                        ] as $prop => $label)
                            <div>
                                <input type="color" wire:model.live="{{ $prop }}" class="h-10 w-full rounded-lg border border-slate-200 cursor-pointer" title="{{ $label }}">
                                <div class="mt-1 text-[10px] text-slate-500 text-center">{{ $label }}</div>
                                @error($prop)<p class="text-[10px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="label">Background Public Samar</label>
                    <div class="rounded-xl border border-dashed border-slate-200 p-4 bg-slate-50/50">
                        <div class="relative flex h-32 items-center justify-center overflow-hidden rounded-lg bg-white ring-1 ring-slate-100">
                            @if ($eventBackgroundImageFile)
                                <img src="{{ $eventBackgroundImageFile->temporaryUrl() }}" alt="" class="h-full w-full object-cover opacity-70">
                            @elseif ($eventExistingBackgroundImage)
                                <img src="{{ asset('storage/'.$eventExistingBackgroundImage) }}" alt="" class="h-full w-full object-cover opacity-70">
                            @else
                                <span class="text-xs text-slate-400">Belum ada background</span>
                            @endif
                            <div class="absolute inset-0 bg-white/45"></div>
                        </div>
                        <input type="file" wire:model="eventBackgroundImageFile" accept="image/*" class="mt-3 text-xs w-full">
                        <div wire:loading wire:target="eventBackgroundImageFile" class="text-xs text-brand-600 mt-1">Mengunggah...</div>
                        @error('eventBackgroundImageFile')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-500 mt-1">Gambar akan tampil samar-samar di background halaman public saat tema ini aktif.</p>
                        @if ($eventBackgroundImageFile || $eventExistingBackgroundImage)
                            <button type="button" wire:click="removeEventBackgroundImage" class="mt-2 text-xs text-red-600 hover:underline">Hapus background</button>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl p-4 ring-1 ring-slate-100"
                    style="background: {{ $eventBackgroundColor }}; color: {{ $eventTextColor }};">
                    <div class="text-xs font-bold uppercase tracking-wider" style="color: {{ $eventPrimaryColor }}">Preview Public</div>
                    <div class="mt-1 font-bold">{{ $eventName ?: 'Nama Tema Event' }}</div>
                    <div class="text-sm opacity-80">{{ $eventMessage ?: 'Pesan banner akan tampil di sini.' }}</div>
                    <div class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                        style="background: {{ $eventPrimaryColor }}; color: {{ $eventSecondaryColor }};">Aksen tombol</div>
                </div>

                <div class="flex flex-wrap gap-4 text-sm text-slate-700">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model="eventRepeatAnnually">
                        Ulang tahunan
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model="eventIsActive">
                        Aktif
                    </label>
                </div>

                <div class="flex gap-2">
                    <button class="btn-primary">
                        <span wire:loading.remove wire:target="saveEventTheme">{{ $eventEditingId ? 'Update Tema' : 'Simpan Tema' }}</span>
                        <span wire:loading wire:target="saveEventTheme">Menyimpan...</span>
                    </button>
                    @if ($eventEditingId)
                        <button type="button" wire:click="resetEventThemeForm" class="btn-ghost">Batal</button>
                    @endif
                </div>
            </form>
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
        <button type="button" wire:click="save" class="btn-primary">
            <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
            <span wire:loading wire:target="save">Menyimpan…</span>
        </button>
    </div>
    <x-confirm-delete title="Hapus Tema Event?" description="Tema ini akan dihapus permanen." :show="(bool) $confirmingDeleteId"
        :label="$confirmingDeleteLabel" />
</div>
