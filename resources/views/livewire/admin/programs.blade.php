<div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-900">Daftar Program</h2>
            <input wire:model.live.debounce.300ms="search" placeholder="Cari…" class="input max-w-xs">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b">
                    <tr><th class="py-2">Judul</th><th>Order</th><th>Aktif</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($items as $p)
                        <tr>
                            <td class="py-2.5"><div class="font-medium text-slate-900">{{ $p->title }}</div><div class="text-xs text-slate-500">{{ $p->short_description }}</div></td>
                            <td>{{ $p->order }}</td>
                            <td>{!! $p->is_active ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">Aktif</span>' : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs">Nonaktif</span>' !!}</td>
                            <td class="text-right">
                                <button wire:click="edit({{ $p->id }})" class="text-brand-600 hover:underline text-xs">Edit</button>
                                <button wire:click="confirmDelete({{ $p->id }}, @js($p->title))" class="text-red-600 hover:underline text-xs ml-2">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-6 text-slate-500">Belum ada program.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
    </div>

    <div class="card">
        <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit Program' : 'Tambah Program' }}</h2>
        <form wire:submit="save" class="space-y-3">
            <div><label class="label">Judul</label><input wire:model="title" class="input">@error('title')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Icon (nama)</label><input wire:model="icon" class="input"></div>
            <div><label class="label">Deskripsi Singkat</label><textarea wire:model="short_description" rows="2" class="textarea"></textarea>@error('short_description')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Deskripsi Lengkap</label>
                <x-rich-editor model="description" :value="$description" placeholder="Deskripsi lengkap program…" />
            </div>
            <div><label class="label">Gambar</label><input type="file" wire:model="image_file" accept="image/*" class="text-xs">@error('image_file')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Urutan</label><input type="number" wire:model="order" class="input"></div>
                <div class="flex items-end"><label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active"> Aktif</label></div>
            </div>
            <div class="flex gap-2">
                <button class="btn-primary">{{ $editingId ? 'Update' : 'Simpan' }}</button>
                @if($editingId)<button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>@endif
            </div>
        </form>
    </div>
</div>
<x-confirm-delete title="Hapus Program?" description="Program ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
