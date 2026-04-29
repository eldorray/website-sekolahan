@props([
    'title' => 'Hapus data?',
    'description' => 'Tindakan ini tidak dapat dibatalkan.',
    'show' => false,
    'label' => '',
])
@if ($show)
    <div
        x-data
        x-init="document.body.style.overflow = 'hidden'"
        x-on:beforeunload.window="document.body.style.overflow = ''"
        @keydown.escape.window="$wire.cancelDelete()"
        class="fixed inset-0 z-[90] flex items-center justify-center p-4"
    >
        {{-- Backdrop --}}
        <div
            x-transition:enter="transition ease-ios duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            wire:click="cancelDelete"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        ></div>

        {{-- Card --}}
        <div
            x-transition:enter="transition ease-ios-spring duration-400"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            class="relative bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl"
        >
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ $description }}
                        @if($label)
                            <span class="block mt-2 font-semibold text-slate-900">"{{ $label }}"</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2">
                <button type="button" wire:click="cancelDelete"
                    class="rounded-full px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-all ease-ios duration-200 active:scale-95">
                    Batal
                </button>
                <button type="button" wire:click="confirmDestroy"
                    class="rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-all ease-ios duration-200 active:scale-95 inline-flex items-center justify-center gap-2">
                    <span wire:loading.remove wire:target="confirmDestroy">Ya, Hapus</span>
                    <span wire:loading wire:target="confirmDestroy">Menghapus…</span>
                </button>
            </div>
        </div>
    </div>
@endif

{{-- Reset body overflow when modal disappears --}}
@if (! $show)
    <div x-data x-init="document.body.style.overflow = ''" class="hidden"></div>
@endif
