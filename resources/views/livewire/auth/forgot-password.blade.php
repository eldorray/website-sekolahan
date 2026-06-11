<div class="card mt-6">
    <h1 class="text-2xl font-bold text-slate-900 mb-1">Lupa Password</h1>
    <p class="text-sm text-slate-500 mb-6">Masukkan email Anda untuk menerima tautan reset password.</p>

    @if ($status)
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-sm text-emerald-700">
            {{ $status }}
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4">
        <div>
            <label class="label">Email</label>
            <input type="email" wire:model="email" class="input" required autofocus>
            @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn-primary w-full justify-center">
            <span wire:loading.remove wire:target="submit">Kirim Tautan Reset</span>
            <span wire:loading wire:target="submit">Mengirim…</span>
        </button>
    </form>

    <p class="mt-4 text-sm text-slate-500">
        <a href="{{ route('login') }}" wire:navigate class="text-brand-600 hover:underline">← Kembali ke Login</a>
    </p>
</div>
