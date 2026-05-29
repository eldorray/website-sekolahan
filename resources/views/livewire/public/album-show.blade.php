<div>
    <section class="py-8 animate-fade-up">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}#gallery" wire:navigate class="text-sm text-brand-600 hover:underline">←
                Kembali</a>
            <div class="mt-4 liquid-glass rounded-[2rem] p-8 sm:p-10 border border-white/80 shadow-sm">
                <div
                    class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-3 border border-white/60 shadow-sm">
                    Galeri Album
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">{{ $album->title }}</h1>
                @if ($album->description)
                    <p class="mt-3 text-slate-600 max-w-3xl leading-relaxed font-light">{{ $album->description }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-3">{{ $album->photos()->count() }} foto</p>
            </div>
        </div>
    </section>

    <section class="pb-14 animate-fade-up delay-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" x-data="{ open: false, currentIndex: 0, photos: @js($photos->map(fn($p) => ['url' => $p->imageUrl(), 'caption' => $p->caption])->values()) }"
            @keydown.escape.window="open = false"
            @keydown.arrow-right.window="open && (currentIndex = (currentIndex + 1) % photos.length)"
            @keydown.arrow-left.window="open && (currentIndex = (currentIndex - 1 + photos.length) % photos.length)">

            @if ($photos->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
                    @foreach ($photos as $i => $p)
                        <button type="button" @click="open = true; currentIndex = {{ $i }}"
                            class="group relative aspect-square overflow-hidden rounded-2xl bg-slate-100 border border-white/80 shadow-sm hover:shadow-md transition">
                            <img src="{{ $p->thumbnailUrl() }}" alt="{{ $p->caption }}" loading="lazy"
                                decoding="async"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @if ($p->caption)
                                <div
                                    class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-2.5 text-[11px] text-white text-left font-medium opacity-0 group-hover:opacity-100 transition">
                                    {{ $p->caption }}
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
                <div class="mt-6">{{ $photos->links() }}</div>
            @else
                <div class="liquid-glass rounded-[2rem] p-10 text-center border border-white/80 shadow-sm">
                    <p class="text-slate-500 text-sm">Belum ada foto di album ini.</p>
                </div>
            @endif

            {{-- Lightbox --}}
            <div x-show="open" x-cloak x-transition.opacity
                class="fixed inset-0 z-[60] bg-black/90 backdrop-blur flex items-center justify-center p-4"
                @click.self="open = false">
                <button type="button" @click="open = false"
                    class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl">✕</button>
                <button type="button" @click="currentIndex = (currentIndex - 1 + photos.length) % photos.length"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center">‹</button>
                <button type="button" @click="currentIndex = (currentIndex + 1) % photos.length"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center">›</button>
                <div class="max-w-5xl w-full max-h-[85vh] flex flex-col items-center justify-center">
                    <template x-for="(photo, idx) in photos" :key="idx">
                        <div x-show="idx === currentIndex" class="w-full">
                            <img :src="photo.url"
                                class="max-h-[80vh] mx-auto rounded-xl object-contain shadow-2xl">
                            <p class="text-center text-white/90 mt-3 text-sm" x-text="photo.caption"></p>
                        </div>
                    </template>
                    <p class="text-center text-white/50 text-xs mt-4">
                        <span x-text="currentIndex + 1"></span> / <span x-text="photos.length"></span>
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
