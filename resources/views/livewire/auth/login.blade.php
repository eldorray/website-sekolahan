<div class="card mt-6">
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Login</h1>
    <p class="text-sm text-slate-500 mb-6">Masuk ke panel staff sekolah.</p>
    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="label">Email</label>
            <input type="email" wire:model="email" class="input" required autofocus>
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="label">Password</label>
            <input type="password" wire:model="password" class="input" required>
            @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" wire:model="remember" class="rounded border-slate-300 text-brand-500 focus:ring-brand-400"> Ingat saya
        </label>
        <button type="submit" class="btn-primary w-full justify-center">
            <span wire:loading.remove wire:target="submit">Masuk</span>
            <span wire:loading wire:target="submit">Memproses…</span>
        </button>
    </form>
</div>
