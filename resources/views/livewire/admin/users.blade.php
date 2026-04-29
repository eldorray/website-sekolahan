<div>
<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card">
        <h2 class="font-bold text-slate-900 mb-3">Pengguna</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b">
                    <tr><th class="py-2">Nama</th><th>Email</th><th>Role</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($items as $u)
                        <tr>
                            <td class="py-2.5">{{ $u->name }}</td>
                            <td class="text-xs">{{ $u->email }}</td>
                            <td><span class="rounded bg-slate-100 px-2 py-0.5 text-xs">{{ $u->role }}</span></td>
                            <td>{!! $u->is_active ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">Aktif</span>' : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs">Off</span>' !!}</td>
                            <td class="text-right">
                                <button wire:click="edit({{ $u->id }})" class="text-brand-600 text-xs hover:underline">Edit</button>
                                @if($u->id !== auth()->id())<button wire:click="confirmDelete({{ $u->id }}, @js($u->name))" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>@endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
    </div>

    <div class="card">
        <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit User' : 'Tambah User' }}</h2>
        <form wire:submit="save" class="space-y-3">
            <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Email</label><input type="email" wire:model="email" class="input">@error('email')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Password {{ $editingId ? '(opsional)' : '' }}</label><input type="password" wire:model="password" class="input">@error('password')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
            <div><label class="label">Role</label><select wire:model="role" class="select"><option value="admin">Admin</option><option value="guru">Guru</option></select></div>
            <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active"> Aktif</label>
            <div class="flex gap-2"><button class="btn-primary">Simpan</button>@if($editingId)<button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>@endif</div>
        </form>
    </div>
</div>
<x-confirm-delete title="Hapus Pengguna?" description="Akun pengguna ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
