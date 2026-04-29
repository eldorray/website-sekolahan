<form wire:submit="submit" class="card space-y-4">
    @if ($sent)<div class="rounded-xl bg-brand-50 p-4 text-sm text-brand-700">Permintaan kunjungan terkirim. Kami akan menghubungi Anda.</div>@endif
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">Nama</label><input wire:model="name" class="input" required>@error('name')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
        <div><label class="label">Email</label><input type="email" wire:model="email" class="input" required>@error('email')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">Telepon</label><input wire:model="phone" class="input" required></div>
        <div><label class="label">Tanggal Kunjungan</label><input type="date" wire:model="visit_date" class="input" required>@error('visit_date')<p class="text-xs text-red-500">{{ $message }}</p>@enderror</div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="label">Jumlah Peserta</label><input type="number" min="1" wire:model="participants" class="input" required></div>
    </div>
    <div><label class="label">Tujuan / Catatan</label><textarea wire:model="purpose" rows="3" class="textarea"></textarea></div>
    <button class="btn-primary">Atur Janji</button>
</form>
