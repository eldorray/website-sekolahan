<div>
    <div class="card">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div>
                <a href="{{ route('admin.gallery') }}" wire:navigate class="text-xs text-brand-600 hover:underline">←
                    Semua Album</a>
                <h2 class="font-bold text-slate-900 text-lg mt-1">{{ $album->title }}</h2>
                <p class="text-xs text-slate-500">{{ $album->photos()->count() }} foto · {!! $album->is_published
                    ? '<span class="text-emerald-600 font-semibold">Publik</span>'
                    : '<span class="text-slate-500 font-semibold">Privat</span>' !!}</p>
            </div>
        </div>

        {{-- Upload area --}}
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/40 p-5 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label class="label">Pilih banyak foto sekaligus</label>
                    <input type="file" wire:model="uploads" multiple accept="image/*" class="text-xs block w-full">
                    @error('uploads.*')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-slate-400 mt-1">Otomatis dikecilkan ke 1600px (utama) dan 480px
                        (thumbnail). Maks 8 MB per foto.</p>
                </div>
                <div class="sm:w-64">
                    <label class="label">Caption default (opsional)</label>
                    <input wire:model="captionDraft" class="input" placeholder="Caption untuk semua foto…">
                </div>
                <div>
                    <button type="button" wire:click="uploadAll" class="btn-primary" wire:loading.attr="disabled"
                        wire:target="uploadAll,uploads">
                        <span wire:loading.remove wire:target="uploadAll,uploads">Upload
                            {{ count($uploads) ? '(' . count($uploads) . ' file)' : '' }}</span>
                        <span wire:loading wire:target="uploadAll,uploads">Memproses…</span>
                    </button>
                </div>
            </div>

            @if (!empty($uploads))
                <div class="mt-4 grid grid-cols-3 sm:grid-cols-6 lg:grid-cols-8 gap-2">
                    @foreach ($uploads as $u)
                        <div class="aspect-square rounded-lg overflow-hidden bg-slate-100 border border-slate-200">
                            <img src="{{ $u->temporaryUrl() }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Existing photos --}}
        @if ($photos->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach ($photos as $p)
                    <div
                        class="relative group rounded-2xl overflow-hidden bg-slate-100 border border-slate-100 shadow-sm">
                        <div class="aspect-square">
                            <img src="{{ $p->thumbnailUrl() }}" alt="" loading="lazy" decoding="async"
                                class="w-full h-full object-cover">
                        </div>
                        @if ($p->caption)
                            <div
                                class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-2 text-[11px] text-white font-medium">
                                {{ $p->caption }}
                            </div>
                        @endif
                        <button wire:click="confirmDelete({{ $p->id }}, 'Foto')"
                            class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 hover:bg-red-500 hover:text-white text-red-600 backdrop-blur flex items-center justify-center text-sm font-bold shadow-md opacity-0 group-hover:opacity-100 transition">
                            ✕
                        </button>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $photos->links() }}</div>
        @else
            <div class="text-center py-10 text-slate-500 text-sm">Belum ada foto di album ini. Unggah foto pertama di
                atas.</div>
        @endif
    </div>

    <x-confirm-delete title="Hapus Foto?" description="Foto akan dihapus permanen." :show="(bool) $confirmingDeleteId"
        :label="$confirmingDeleteLabel" />
</div>
