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
                            <th class="py-2">Preview</th>
                            <th>Judul</th>
                            <th>Order</th>
                            <th>Aktif</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($items as $b)
                            <tr>
                                <td data-label="Preview" class="py-2.5">
                                    <img src="{{ $b->previewUrl() }}" alt="{{ $b->title }}"
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
                                <td colspan="5" class="text-center py-6 text-slate-500">Belum ada brosur.</td>
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
                    <label class="label">Gambar Preview</label>
                    @if ($preview_file)
                        <img src="{{ $preview_file->temporaryUrl() }}"
                            class="w-32 h-40 object-cover rounded-lg mb-2 border border-slate-100">
                    @elseif ($existing_preview)
                        <img src="{{ asset('storage/' . $existing_preview) }}"
                            class="w-32 h-40 object-cover rounded-lg mb-2 border border-slate-100">
                    @endif
                    <input type="file" wire:model="preview_file" accept="image/*" class="text-xs">
                    @error('preview_file')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Otomatis dikecilkan ke maks 1200px lebar.</p>
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
                    <button class="btn-primary" wire:loading.attr="disabled" wire:target="save,preview_file,file_pdf">
                        <span wire:loading.remove
                            wire:target="save,preview_file,file_pdf">{{ $editingId ? 'Update' : 'Simpan' }}</span>
                        <span wire:loading wire:target="save,preview_file,file_pdf">Memproses…</span>
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-confirm-delete title="Hapus Brosur?" description="Brosur ini akan dihapus permanen." :show="(bool) $confirmingDeleteId"
        :label="$confirmingDeleteLabel" />
</div>
