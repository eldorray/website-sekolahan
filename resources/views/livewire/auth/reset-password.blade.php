<div class="card mt-6">
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Reset Password</h1>
    <p class="text-sm text-slate-500 mb-6">Buat password baru untuk akun Anda.</p>

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="label">Email</label>
            <input type="email" wire:model="email" class="input" required>
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label">Password Baru</label>
            <input type="password" wire:model="password" class="input" required autofocus>
            @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label">Konfirmasi Password</label>
            <input type="password" wire:model="password_confirmation" class="input" required>
        </div>
        <button type="submit" class="btn-primary w-full justify-center">
            <span wire:loading.remove wire:target="submit">Reset Password</span>
            <span wire:loading wire:target="submit">Memproses…</span>
        </button>
    </form>

    <p class="mt-4 text-sm text-slate-500">
        <a href="{{ route('login') }}" wire:navigate class="text-brand-600 hover:underline">← Kembali ke Login</a>
    </p>
</div>
