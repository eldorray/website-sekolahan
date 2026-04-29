<form wire:submit="save" class="card max-w-2xl">
    <h2 class="font-bold text-slate-900 mb-4">Profil Saya</h2>
    <div class="flex items-center gap-4 mb-5">
        <img src="{{ auth()->user()->photoUrl() }}" class="h-20 w-20 rounded-full object-cover ring-2 ring-brand-100">
        <div>
            <label class="label">Foto Baru</label>
            <input type="file" wire:model="photo_file" class="text-xs">
            @error('photo_file')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
        <div><label class="label">Email</label><input type="email" wire:model="email" class="input">@error('email')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
        <div><label class="label">Posisi</label><input wire:model="position" class="input"></div>
        <div><label class="label">Telepon</label><input wire:model="phone" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Bio</label><textarea wire:model="bio" rows="3" class="textarea"></textarea></div>
        <div><label class="label">Instagram</label><input wire:model="instagram" class="input"></div>
        <div><label class="label">Facebook</label><input wire:model="facebook" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Password Baru (opsional)</label><input type="password" wire:model="password" class="input">@error('password')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
    </div>
    <div class="mt-5"><button class="btn-primary">Simpan Profil</button></div>
</form>
