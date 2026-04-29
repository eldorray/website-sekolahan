@php
    use App\Models\News;
    $news = News::with('author')->where('slug', $slug)->where('is_published', true)->firstOrFail();
    $related = News::where('is_published', true)->where('id', '!=', $news->id)->orderByDesc('published_at')->take(3)->get();
@endphp
<x-layouts.public>
    <article class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('news.index') }}" class="text-sm text-brand-600 hover:underline">← Semua Berita</a>
            <span class="mt-4 inline-block rounded-md bg-brand-500 px-2 py-1 text-[10px] font-bold text-white">{{ $news->category }}</span>
            <h1 class="mt-3 text-4xl font-extrabold text-slate-900 leading-tight">{{ $news->title }}</h1>
            <div class="mt-3 flex items-center gap-3 text-sm text-slate-500">
                <span>{{ $news->author->name ?? '-' }}</span><span>•</span>
                <span>{{ $news->published_at?->translatedFormat('d F Y') }}</span>
            </div>
            <div class="mt-6 aspect-video rounded-2xl overflow-hidden">
                <img src="{{ $news->imageUrl() }}" class="h-full w-full object-cover" alt="">
            </div>
            <div class="mt-6 prose prose-slate max-w-none">{!! $news->content !!}</div>
        </div>
    </article>

    @if ($related->count())
        <section class="py-10 bg-slate-50/40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-5">Berita Terkait</h2>
                <div class="grid sm:grid-cols-3 gap-5">
                    @foreach ($related as $r)
                        <a href="{{ route('news.show', $r->slug) }}" class="group">
                            <div class="aspect-[4/3] rounded-xl overflow-hidden mb-3"><img src="{{ $r->imageUrl() }}" class="h-full w-full object-cover group-hover:scale-105 transition" alt=""></div>
                            <h3 class="font-bold text-slate-900 group-hover:text-brand-600">{{ $r->title }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.public>
