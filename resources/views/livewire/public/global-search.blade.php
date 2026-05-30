<div x-data="{
    open: false,
    activeIndex: 0,
    openModal() {
        this.open = true;
        this.activeIndex = 0;
        this.$nextTick(() => this.$refs.searchInput?.focus());
    },
    closeModal() {
        this.open = false;
        $wire.set('q', '');
    },
    move(dir) {
        const count = this.$refs.list ? this.$refs.list.querySelectorAll('[data-result]').length : 0;
        if (!count) return;
        this.activeIndex = (this.activeIndex + dir + count) % count;
        this.$nextTick(() => {
            const el = this.$refs.list.querySelectorAll('[data-result]')[this.activeIndex];
            el?.scrollIntoView({ block: 'nearest' });
        });
    },
    enter() {
        const el = this.$refs.list?.querySelectorAll('[data-result]')[this.activeIndex];
        if (el) el.click();
    }
}" @keydown.window.prevent.cmd.k="openModal()" @keydown.window.prevent.ctrl.k="openModal()"
    @open-global-search.window="openModal()">

    {{-- Trigger button --}}
    <button type="button" @click="openModal()"
        class="inline-flex items-center gap-2 rounded-full bg-white/70 hover:bg-white border border-white/60 text-slate-500 hover:text-slate-700 pl-3 pr-3 sm:pr-4 py-2 text-sm font-medium shadow-sm transition apple-transition">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
            class="h-4 w-4">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
        <span class="hidden sm:inline">Cari</span>
        <span
            class="hidden lg:inline-flex items-center gap-0.5 ml-1 text-[10px] font-bold text-slate-400 border border-slate-200 rounded px-1.5 py-0.5">
            ⌘K
        </span>
    </button>

    {{-- Spotlight overlay --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak
            class="fixed inset-0 z-[80] flex items-start justify-center px-4 pt-[12vh] sm:pt-[16vh]"
            @keydown.escape.window="closeModal()" @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)"
            @keydown.enter.prevent="enter()">

            {{-- Backdrop --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="closeModal()"
                class="absolute inset-0 bg-slate-900/40 backdrop-blur-md"></div>

            {{-- Panel --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
                class="relative w-full max-w-xl origin-top rounded-2xl bg-white/95 dark:bg-slate-800/95 backdrop-blur-2xl shadow-2xl ring-1 ring-black/5 overflow-hidden">

                {{-- Search input --}}
                <div class="flex items-center gap-3 px-4 border-b border-slate-100 dark:border-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="h-5 w-5 text-slate-400 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input x-ref="searchInput" type="text" wire:model.live.debounce.250ms="q"
                        @input="activeIndex = 0" placeholder="Cari berita, program, galeri, halaman…"
                        class="w-full bg-transparent py-4 text-base text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none">
                    <div wire:loading wire:target="q" class="shrink-0">
                        <div class="w-4 h-4 border-2 border-brand-500 border-t-transparent rounded-full animate-spin">
                        </div>
                    </div>
                    <button type="button" @click="closeModal()"
                        class="shrink-0 text-[11px] font-bold text-slate-400 border border-slate-200 dark:border-slate-600 rounded px-1.5 py-0.5 hover:bg-slate-100 dark:hover:bg-slate-700 transition">ESC</button>
                </div>

                {{-- Results --}}
                <div x-ref="list" class="max-h-[55vh] overflow-y-auto p-2">
                    @if (strlen(trim($q)) < 2)
                        <div class="px-4 py-10 text-center">
                            <div
                                class="mx-auto w-12 h-12 rounded-2xl bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-slate-300 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                            <p class="text-sm text-slate-400">Ketik minimal 2 huruf untuk mencari.</p>
                            <p class="text-xs text-slate-300 mt-1">Berita · Program · Galeri · Halaman</p>
                        </div>
                    @elseif (count($results) === 0)
                        <div class="px-4 py-10 text-center">
                            <p class="text-sm text-slate-500 dark:text-slate-300">Tidak ada hasil untuk "<span
                                    class="font-semibold">{{ $q }}</span>".</p>
                            <p class="text-xs text-slate-400 mt-1">Coba kata kunci lain.</p>
                        </div>
                    @else
                        @foreach ($results as $i => $r)
                            <a href="{{ $r['url'] }}" wire:navigate data-result @click="closeModal()"
                                @mouseenter="activeIndex = {{ $i }}"
                                :class="activeIndex === {{ $i }} ? 'bg-brand-50 dark:bg-slate-700' : ''"
                                class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition group">
                                <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-700 group-hover:bg-white dark:group-hover:bg-slate-600 flex items-center justify-center text-brand-600 shrink-0 transition"
                                    :class="activeIndex === {{ $i }} ? 'bg-white dark:bg-slate-600' : ''">
                                    <x-icon :name="$r['icon']" class="h-4 w-4" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">
                                        {{ $r['title'] }}</div>
                                    @if ($r['subtitle'])
                                        <div class="text-xs text-slate-400 truncate">{{ $r['subtitle'] }}</div>
                                    @endif
                                </div>
                                <span
                                    class="shrink-0 text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full">{{ $r['type'] }}</span>
                            </a>
                        @endforeach
                    @endif
                </div>

                {{-- Footer hint --}}
                <div
                    class="flex items-center justify-between px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 text-[11px] text-slate-400">
                    <span class="flex items-center gap-2">
                        <kbd
                            class="font-sans border border-slate-200 dark:border-slate-600 rounded px-1.5 py-0.5">↑</kbd>
                        <kbd
                            class="font-sans border border-slate-200 dark:border-slate-600 rounded px-1.5 py-0.5">↓</kbd>
                        navigasi
                    </span>
                    <span class="flex items-center gap-2">
                        <kbd
                            class="font-sans border border-slate-200 dark:border-slate-600 rounded px-1.5 py-0.5">↵</kbd>
                        buka
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>
