<div class="card mt-6">
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Verifikasi Dua Faktor</h1>
    <p class="text-sm text-slate-500 mb-6">Masukkan kode 6 digit dari aplikasi authenticator Anda, atau kode pemulihan.</p>

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="label">Kode Verifikasi</label>
            <input type="text" wire:model="code" inputmode="numeric" autocomplete="one-time-code" class="input"
                required autofocus>
            @error('code')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary w-full justify-center">
            <span wire:loading.remove wire:target="submit">Verifikasi</span>
            <span wire:loading wire:target="submit">Memeriksa…</span>
        </button>
    </form>

    <p class="mt-4 text-sm text-slate-500">
        <a href="{{ route('login') }}" wire:navigate class="text-brand-600 hover:underline">← Batal</a>
    </p>
</div>
