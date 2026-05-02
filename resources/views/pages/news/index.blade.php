@php
    use App\Models\News;
    $items = News::where('is_published', true)->orderByDesc('published_at')->paginate(9);
@endphp
<x-layouts.public>
    <section class="py-8 animate-fade-up">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto liquid-glass rounded-[2rem] p-8 sm:p-12 border border-white/80 shadow-sm">
                <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-4 border border-white/60 shadow-sm">
                    Informasi Terkini
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 drop-shadow-sm">Berita & <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-brand-700">Artikel</span></h1>
            </div>
        </div>
    </section>
    <section class="py-12 animate-fade-up delay-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($items as $n)
                <a href="{{ route('news.show', $n->slug) }}" class="liquid-glass rounded-[2rem] p-4 border border-white/80 shadow-sm apple-transition hover:-translate-y-2 hover:shadow-xl group relative overflow-hidden flex flex-col h-full">
                    <div class="aspect-[4/3] rounded-[1.5rem] overflow-hidden mb-4 relative shadow-sm border border-white/40">
                        <img src="{{ $n->imageUrl() }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" alt="">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <span class="absolute top-3 left-3 rounded-full bg-white/90 backdrop-blur-sm px-3 py-1 text-[10px] font-bold text-brand-600 shadow-sm uppercase tracking-wider">{{ $n->category }}</span>
                    </div>
                    <div class="flex-grow px-2">
                        <div class="flex items-center gap-2 text-[11px] font-medium text-slate-500 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            {{ $n->published_at?->translatedFormat('d M Y') }}
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg group-hover:text-brand-600 transition-colors duration-300 mb-2 line-clamp-2 leading-snug">{{ $n->title }}</h3>
                        <p class="text-[13px] font-medium text-slate-600 line-clamp-2 leading-relaxed">{{ $n->excerpt }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-10">
            <div class="liquid-glass rounded-full px-6 py-2 border border-white/80 shadow-sm inline-block">
                {{ $items->links() }}
            </div>
        </div>
    </section>
</x-layouts.public>
