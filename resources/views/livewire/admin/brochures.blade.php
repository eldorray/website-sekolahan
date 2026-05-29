<div>
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-900">Daftar Brosur</h2>
                <input wire:model.live.debounce.300ms="search" placeholder="Cari…" class="input max-w-xs">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm responsive-table">
                    <thead class="text-left text-slate-500 border-b">
                        <tr>
                            <th class="py-2">Cover</th>
                            <th>Judul</th>
                            <th>Gambar</th>
                            <th>Order</th>
                            <th>Aktif</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($items as $b)
                            <tr>
                                <td data-label="Cover" class="py-2.5">
                                    <img src="{{ $b->coverUrl() }}" alt="{{ $b->title }}"
                                        class="w-16 h-20 object-cover rounded-lg border border-slate-100 shadow-sm">
                                </td>
                                <td data-label="Judul">
                                    <div class="font-medium text-slate-900">{{ $b->title }}</div>
                                    @if ($b->subtitle)
                                        <div class="text-xs text-slate-500">{{ $b->subtitle }}</div>
                                    @endif
                                    @if ($b->file)
                                        <a href="{{ $b->fileUrl() }}" target="_blank"
                                            class="inline-flex items-center gap-1 mt-1 text-xs text-brand-600 hover:underline">
                                            PDF terlampir
                                        </a>
                                    @endif
                                </td>
                                <td data-label="Gambar">
                                    <span
                                        class="rounded-full bg-slate-100 text-slate-700 px-2 py-0.5 text-xs font-semibold">{{ $b->images->count() }}</span>
                                </td>
                                <td data-label="Order">{{ $b->order }}</td>
                                <td data-label="Status">{!! $b->is_active
                                    ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">Aktif</span>'
                                    : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs">Nonaktif</span>' !!}</td>
                                <td data-label="Aksi" class="text-right whitespace-nowrap">
                                    <button wire:click="edit({{ $b->id }})"
                                        class="text-brand-600 hover:underline text-xs">Edit</button>
                                    <button
                                        wire:click="confirmDelete({{ $b->id }}, @js($b->title))"
                                        class="text-red-600 hover:underline text-xs ml-2">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-500">Belum ada brosur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </div>

        <div class="card">
            <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit Brosur' : 'Tambah Brosur' }}</h2>
            <form wire:submit="save" class="space-y-3" enctype="multipart/form-data">
                <div>
                    <label class="label">Judul</label>
                    <input wire:model="title" class="input">
                    @error('title')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="label">Subjudul / Deskripsi singkat</label>
                    <input wire:model="subtitle" class="input" placeholder="Mis. Tahun Ajaran 2026/2027">
                </div>

                <div>
                    <label class="label">Gambar Brosur (boleh banyak)</label>
                    <input type="file" wire:model="images" multiple accept="image/*" class="text-xs block w-full">
                    @error('images')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Otomatis dikecilkan ke 1200px (utama) & 480px
                        (thumbnail). Maks 6 MB per gambar.</p>

                    @if (!empty($images))
                        <div class="mt-3 grid grid-cols-3 gap-2">
                            @foreach ($images as $tmp)
                                <div
                                    class="aspect-[3/4] rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
                                    <img src="{{ $tmp->temporaryUrl() }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($editingId && $editingImages->count())
                        <div class="mt-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Gambar
                                Tersimpan</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach ($editingImages as $img)
                                    <div
                                        class="relative group rounded-lg overflow-hidden border border-slate-200 bg-slate-50">
                                        <div class="aspect-[3/4]">
                                            <img src="{{ $img->thumbnailUrl() }}" loading="lazy" decoding="async"
                                                class="w-full h-full object-cover">
                                        </div>
                                        @if ($img->is_cover)
                                            <span
                                                class="absolute top-1.5 left-1.5 bg-brand-600 text-white text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded shadow">Cover</span>
                                        @endif
                                        <div
                                            class="absolute inset-x-1 bottom-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                            @unless ($img->is_cover)
                                                <button type="button" wire:click="setCover({{ $img->id }})"
                                                    class="flex-1 px-1.5 py-1 rounded bg-white/90 hover:bg-white text-[10px] font-bold text-slate-700 shadow">Cover</button>
                                            @endunless
                                            <button type="button" wire:click="deleteImage({{ $img->id }})"
                                                wire:confirm="Hapus gambar ini?"
                                                class="flex-1 px-1.5 py-1 rounded bg-red-500/90 hover:bg-red-600 text-[10px] font-bold text-white shadow">Hapus</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <label class="label">File PDF (opsional)</label>
                    @if ($existing_file)
                        <a href="{{ asset('storage/' . $existing_file) }}" target="_blank"
                            class="text-xs text-brand-600 hover:underline block mb-1">PDF saat ini</a>
                    @endif
                    <input type="file" wire:model="file_pdf" accept="application/pdf" class="text-xs">
                    @error('file_pdf')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Urutan</label>
                        <input type="number" wire:model="order" class="input">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="is_active"> Aktif
                        </label>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button class="btn-primary" wire:loading.attr="disabled" wire:target="save,images,file_pdf">
                        <span wire:loading.remove
                            wire:target="save,images,file_pdf">{{ $editingId ? 'Update' : 'Simpan' }}</span>
                        <span wire:loading wire:target="save,images,file_pdf">Memproses…</span>
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-confirm-delete title="Hapus Brosur?" description="Brosur dan seluruh gambarnya akan dihapus permanen."
        :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
