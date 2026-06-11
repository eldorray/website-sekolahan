<form wire:submit="submit" class="space-y-6">
    <x-honeypot livewire-model="extraFields" />
    @error('throttle')
        <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-sm">{{ $message }}</div>
    @enderror
    @if ($sent)
        <div
            class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-3 text-emerald-800">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-xs">
                <p class="font-bold">Pesan Terkirim dengan Sukses!</p>
                <p class="mt-0.5">Terima kasih, tim kami akan segera membalas lewat e-mail.</p>
            </div>
        </div>
    @endif

    {{-- Name --}}
    <div class="relative" x-data="{ focused: false }">
        <input type="text" wire:model="name" id="contact-name" required @focus="focused = true"
            @blur="focused = false"
            class="w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none peer"
            :class="focused || $wire.name ? 'pt-6 pb-2 border-brand-600 ring-1 ring-brand-500' :
                'border-slate-200 hover:border-slate-300 bg-white'"
            placeholder=" ">
        <label for="contact-name"
            class="absolute left-5 transition-all duration-300 pointer-events-none text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand-600 peer-focus:font-bold top-2 text-brand-600 font-bold">
            Nama Lengkap Anda
        </label>
        @error('name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div class="relative" x-data="{ focused: false }">
        <input type="email" wire:model="email" id="contact-email" required @focus="focused = true"
            @blur="focused = false"
            class="w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none peer"
            :class="focused || $wire.email ? 'pt-6 pb-2 border-brand-600 ring-1 ring-brand-500' :
                'border-slate-200 hover:border-slate-300 bg-white'"
            placeholder=" ">
        <label for="contact-email"
            class="absolute left-5 transition-all duration-300 pointer-events-none text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand-600 peer-focus:font-bold top-2 text-brand-600 font-bold">
            Alamat Email Aktif
        </label>
        @error('email')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Phone & Subject --}}
    <div class="grid sm:grid-cols-2 gap-6">
        <div class="relative" x-data="{ focused: false }">
            <input type="text" wire:model="phone" id="contact-phone" @focus="focused = true" @blur="focused = false"
                class="w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none peer"
                :class="focused || $wire.phone ? 'pt-6 pb-2 border-brand-600 ring-1 ring-brand-500' :
                    'border-slate-200 hover:border-slate-300 bg-white'"
                placeholder=" ">
            <label for="contact-phone"
                class="absolute left-5 transition-all duration-300 pointer-events-none text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand-600 peer-focus:font-bold top-2 text-brand-600 font-bold">
                No. Telepon (Opsional)
            </label>
        </div>

        <div class="relative" x-data="{ focused: false }">
            <input type="text" wire:model="subject" id="contact-subject" required @focus="focused = true"
                @blur="focused = false"
                class="w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none peer"
                :class="focused || $wire.subject ? 'pt-6 pb-2 border-brand-600 ring-1 ring-brand-500' :
                    'border-slate-200 hover:border-slate-300 bg-white'"
                placeholder=" ">
            <label for="contact-subject"
                class="absolute left-5 transition-all duration-300 pointer-events-none text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand-600 peer-focus:font-bold top-2 text-brand-600 font-bold">
                Subjek Pertanyaan
            </label>
            @error('subject')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Message --}}
    <div class="relative" x-data="{ focused: false }">
        <textarea wire:model="message" id="contact-message" required rows="4" @focus="focused = true"
            @blur="focused = false"
            class="w-full px-5 py-4 rounded-2xl border text-sm transition-all duration-300 outline-none resize-none peer"
            :class="focused || $wire.message ? 'pt-6 pb-2 border-brand-600 ring-1 ring-brand-500' :
                'border-slate-200 hover:border-slate-300 bg-white'"
            placeholder=" "></textarea>
        <label for="contact-message"
            class="absolute left-5 transition-all duration-300 pointer-events-none text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand-600 peer-focus:font-bold top-2 text-brand-600 font-bold">
            Tulis Detail Pesan Anda...
        </label>
        @error('message')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Submit Button --}}
    <button type="submit"
        class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 rounded-2xl transition-all duration-300 flex items-center justify-center space-x-2 shadow-lg"
        wire:loading.attr="disabled">
        <span wire:loading.remove>Kirim Pesan</span>
        <svg wire:loading.remove class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
        </svg>
        <div wire:loading class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
    </button>
</form>
