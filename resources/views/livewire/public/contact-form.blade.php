<form wire:submit="submit" class="card space-y-4">
    @if ($sent)
        <div class="rounded-xl bg-brand-50 p-4 text-sm text-brand-700">Pesan Anda telah terkirim. Terima kasih!</div>
    @endif
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="label">Nama</label>
            <input type="text" wire:model="name" class="input" required>
            @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label">Email</label>
            <input type="email" wire:model="email" class="input" required>
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="label">No. Telepon</label>
            <input type="text" wire:model="phone" class="input">
        </div>
        <div>
            <label class="label">Subjek</label>
            <input type="text" wire:model="subject" class="input" required>
            @error('subject')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
    <div>
        <label class="label">Pesan</label>
        <textarea wire:model="message" rows="5" class="textarea" required></textarea>
        @error('message')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-primary">Kirim Pesan</button>
</form>
