@php
    use App\Models\News;
    $items = News::where('is_published', true)->orderByDesc('published_at')->paginate(9);
@endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-brand-600 font-semibold mb-2">Informasi Terkini</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900">Berita & <span class="text-brand-500">Artikel</span></h1>
        </div>
    </section>
    <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($items as $n)
                <a href="{{ route('news.show', $n->slug) }}" class="group">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-3 relative">
                        <img src="{{ $n->imageUrl() }}" class="h-full w-full object-cover group-hover:scale-105 transition" alt="">
                        <span class="absolute top-3 left-3 rounded-md bg-brand-500 px-2 py-1 text-[10px] font-bold text-white">{{ $n->category }}</span>
                    </div>
                    <div class="text-xs text-slate-500 mb-1">{{ $n->published_at?->translatedFormat('d M Y') }}</div>
                    <h3 class="font-bold text-slate-900 group-hover:text-brand-600 transition">{{ $n->title }}</h3>
                    <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ $n->excerpt }}</p>
                </a>
            @endforeach
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
            {{ $items->links() }}
        </div>
    </section>
</x-layouts.public>
