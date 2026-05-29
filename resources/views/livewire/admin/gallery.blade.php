<div>
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-900">Album Galeri</h2>
                <input wire:model.live.debounce.300ms="search" placeholder="Cari…" class="input max-w-xs">
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($items as $a)
                    <div
                        class="rounded-2xl border border-slate-100 overflow-hidden bg-white shadow-sm hover:shadow-md transition group">
                        <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                            <img src="{{ $a->coverUrl() }}" alt="{{ $a->title }}" loading="lazy" decoding="async"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-2 right-2">
                                {!! $a->is_published
                                    ? '<span class="rounded-full bg-emerald-500/90 text-white px-2 py-0.5 text-[10px] font-bold backdrop-blur">Publik</span>'
                                    : '<span class="rounded-full bg-slate-700/80 text-white px-2 py-0.5 text-[10px] font-bold backdrop-blur">Privat</span>' !!}
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-sm text-slate-900 line-clamp-1">{{ $a->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $a->photos_count }} foto</p>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded">
                                    #{{ $a->order }}
                                </span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <a href="{{ route('admin.gallery.photos', $a->id) }}" wire:navigate
                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 px-2.5 py-1 rounded-full transition">
                                    Foto →
                                </a>
                                <button wire:click="edit({{ $a->id }})"
                                    class="text-[11px] font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-full transition">Edit</button>
                                <button wire:click="confirmDelete({{ $a->id }}, @js($a->title))"
                                    class="text-[11px] font-semibold text-red-700 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-full transition">Hapus</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-slate-500 text-sm">Belum ada album. Buat album
                        pertama di samping.</div>
                @endforelse
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </div>

        <div class="card">
            <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit Album' : 'Tambah Album' }}</h2>
            <form wire:submit="save" class="space-y-3">
                <div>
                    <label class="label">Judul</label>
                    <input wire:model="title" class="input">
                    @error('title')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="label">Deskripsi</label>
                    <textarea wire:model="description" rows="3" class="textarea"></textarea>
                </div>
                <div>
                    <label class="label">Gambar Cover</label>
                    @if ($cover_file)
                        <img src="{{ $cover_file->temporaryUrl() }}"
                            class="w-full aspect-[4/3] object-cover rounded-lg mb-2 border border-slate-100">
                    @elseif ($existing_cover)
                        <img src="{{ asset('storage/' . $existing_cover) }}"
                            class="w-full aspect-[4/3] object-cover rounded-lg mb-2 border border-slate-100">
                    @endif
                    <input type="file" wire:model="cover_file" accept="image/*" class="text-xs">
                    @error('cover_file')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Otomatis dikecilkan. Kosongkan untuk pakai foto pertama.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Urutan</label>
                        <input type="number" wire:model="order" class="input">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="is_published"> Publikasikan
                        </label>
                    </div>
                </div>
                <div class="flex gap-2 pt-2">
                    <button class="btn-primary" wire:loading.attr="disabled" wire:target="save,cover_file">
                        <span wire:loading.remove
                            wire:target="save,cover_file">{{ $editingId ? 'Update' : 'Simpan' }}</span>
                        <span wire:loading wire:target="save,cover_file">Memproses…</span>
                    </button>
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <x-confirm-delete title="Hapus Album?" description="Album dan seluruh foto di dalamnya akan dihapus permanen."
        :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
