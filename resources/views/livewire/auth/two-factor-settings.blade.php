<div class="max-w-2xl">
    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <div class="card">
        <h2 class="font-bold text-slate-900 mb-1">Autentikasi Dua Faktor (2FA)</h2>
        <p class="text-sm text-slate-500 mb-4">Tambahkan lapisan keamanan ekstra menggunakan aplikasi authenticator
            (Google Authenticator, Authy, dll).</p>

        @if ($enabled)
            <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2 text-sm text-emerald-700 mb-4">
                ✓ 2FA aktif untuk akun Anda.
            </div>
            <button wire:click="disable" class="btn-secondary text-red-600 ring-red-200 hover:bg-red-50">
                Nonaktifkan 2FA
            </button>
        @elseif ($enrolling)
            <p class="text-sm text-slate-600 mb-3">1. Pindai QR berikut dengan aplikasi authenticator Anda:</p>
            <div class="inline-block rounded-xl bg-white p-3 ring-1 ring-slate-200 mb-3">
                <img src="{{ $qrCode }}" alt="QR Code 2FA" class="w-44 h-44">
            </div>
            <p class="text-xs text-slate-500 mb-4">Atau masukkan kunci manual: <code class="font-mono">{{ $secret }}</code></p>

            @if (!empty($recoveryCodes))
                <p class="text-sm text-slate-600 mb-2">2. Simpan kode pemulihan ini di tempat aman:</p>
                <div class="grid grid-cols-2 gap-1 text-xs font-mono bg-slate-50 rounded-lg p-3 mb-4">
                    @foreach ($recoveryCodes as $rc)
                        <span>{{ $rc }}</span>
                    @endforeach
                </div>
            @endif

            <form wire:submit="confirm" class="flex items-end gap-2">
                <div>
                    <label class="label">3. Masukkan kode dari aplikasi</label>
                    <input type="text" wire:model="confirmCode" inputmode="numeric" class="input" required>
                    @error('confirmCode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn-primary">Aktifkan</button>
            </form>
        @else
            <button wire:click="startEnroll" class="btn-primary">Aktifkan 2FA</button>
        @endif
    </div>
</div>
