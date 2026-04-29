<div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900">Daftar Berita</h2>
            <input wire:model.live.debounce.300ms="search" placeholder="Cari…" class="input max-w-xs">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b">
                    <tr><th class="py-2">Judul</th><th>Kategori</th><th>Tanggal</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($items as $n)
                        <tr>
                            <td class="py-2.5"><div class="font-medium text-slate-900">{{ Str::limit($n->title, 60) }}</div><div class="text-xs text-slate-500">oleh {{ $n->author->name ?? '-' }}</div></td>
                            <td><span class="rounded bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">{{ $n->category }}</span></td>
                            <td>{{ $n->published_at?->format('d M Y') }}</td>
                            <td>{!! $n->is_published ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">Terbit</span>' : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs">Draft</span>' !!}</td>
                            <td class="text-right">
                                <button wire:click="edit({{ $n->id }})" class="text-brand-600 text-xs hover:underline">Edit</button>
                                <button wire:click="confirmDelete({{ $n->id }}, @js($n->title))" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Belum ada berita.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
    </div>

    <div class="card">
        <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit Berita' : 'Tulis Berita' }}</h2>
        <form wire:submit="save" class="space-y-3">
            <div><label class="label">Judul</label><input wire:model="title" class="input">@error('title')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Kategori</label>
                <select wire:model="category" class="select"><option>KEGIATAN</option><option>PRESTASI</option><option>ARTIKEL</option><option>PENGUMUMAN</option></select>
            </div>
            <div><label class="label">Excerpt</label><textarea wire:model="excerpt" rows="2" class="textarea"></textarea>@error('excerpt')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Konten</label>
                <x-rich-editor model="content" :value="$content" placeholder="Tulis isi berita…" />
                @error('content')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div><label class="label">Tanggal Terbit</label><input type="date" wire:model="published_at" class="input"></div>
            <div><label class="label">Gambar</label><input type="file" wire:model="image_file" accept="image/*" class="text-xs">@error('image_file')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_published"> Publikasikan</label>
            <div class="flex gap-2 pt-1">
                <button class="btn-primary">{{ $editingId ? 'Update' : 'Simpan' }}</button>
                @if($editingId)<button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>@endif
            </div>
        </form>
    </div>
</div>
<x-confirm-delete title="Hapus Berita?" description="Berita ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
