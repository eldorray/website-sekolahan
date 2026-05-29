<div>
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-900">Daftar Guru</h2>
                <input wire:model.live.debounce.300ms="search" placeholder="Cari…" class="input max-w-xs">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm responsive-table">
                    <thead class="text-left text-slate-500 border-b">
                        <tr>
                            <th class="py-2">Nama</th>
                            <th>Posisi</th>
                            <th>Email</th>
                            <th>Aktif</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($items as $u)
                            <tr>
                                <td data-label="Nama" class="py-2.5">
                                    <div class="flex items-center gap-2"><img src="{{ $u->photoUrl() }}"
                                            class="h-8 w-8 rounded-full object-cover"><span>{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td data-label="Posisi">{{ $u->position }}</td>
                                <td data-label="Email" class="text-xs break-all">{{ $u->email }}</td>
                                <td data-label="Status">{!! $u->is_active
                                    ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">Aktif</span>'
                                    : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs">Off</span>' !!}</td>
                                <td data-label="Aksi" class="text-right">
                                    <button wire:click="edit({{ $u->id }})"
                                        class="text-brand-600 text-xs hover:underline">Edit</button>
                                    <button
                                        wire:click="confirmDelete({{ $u->id }}, @js($u->name))"
                                        class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-slate-500">Belum ada guru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </div>

        <div class="card">
            <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit Guru' : 'Tambah Guru' }}</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3">
                    <div class="flex items-center gap-2 text-red-700 text-sm font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                        Data belum bisa disimpan
                    </div>
                    <ul class="mt-1.5 list-disc list-inside text-xs text-red-600 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form wire:submit="save" class="space-y-3">
                <div><label class="label">Nama</label><input wire:model.blur="name"
                        class="input @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Email</label><input type="email" wire:model.blur="email"
                        class="input @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Password
                        {{ $editingId ? '(kosongkan jika tidak ganti)' : '' }}</label><input type="password"
                        wire:model.blur="password" class="input @error('password') border-red-400 @enderror">
                    @error('password')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Posisi</label><input wire:model="position" class="input">
                    @error('position')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Telepon</label><input wire:model="phone" class="input">
                    @error('phone')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Bio</label>
                    <textarea wire:model="bio" rows="2" class="textarea"></textarea>
                    @error('bio')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Instagram</label><input wire:model.blur="instagram"
                        class="input @error('instagram') border-red-400 @enderror"
                        placeholder="https://instagram.com/namaakun">
                    @error('instagram')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Facebook</label><input wire:model.blur="facebook"
                        class="input @error('facebook') border-red-400 @enderror"
                        placeholder="https://facebook.com/namaakun">
                    @error('facebook')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div><label class="label">Foto</label><input type="file" wire:model="photo_file" class="text-xs">
                    @error('photo_file')
                        <p class="text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" wire:model="is_active">
                    Aktif</label>
                <div class="flex gap-2"><button class="btn-primary">{{ $editingId ? 'Update' : 'Simpan' }}</button>
                    @if ($editingId)
                        <button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    <x-confirm-delete title="Hapus Guru?" description="Akun guru ini akan dihapus permanen." :show="(bool) $confirmingDeleteId"
        :label="$confirmingDeleteLabel" />
</div>
