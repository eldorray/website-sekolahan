<div>
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        @foreach ($stats as $s)
            <div class="card"><div class="text-xs text-slate-500">{{ $s['label'] }}</div><div class="text-3xl font-bold text-slate-900 mt-2">{{ $s['value'] }}</div></div>
        @endforeach
    </div>
    <div class="card">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-bold text-slate-900">Berita Terbaru Saya</h3>
            <a href="{{ route('guru.news') }}" class="btn-ghost">Kelola Berita →</a>
        </div>
        <div class="divide-y">
            @forelse ($latest as $n)
                <div class="py-2.5 flex justify-between">
                    <div>
                        <div class="font-medium text-sm">{{ $n->title }}</div>
                        <div class="text-xs text-slate-500">{{ $n->published_at?->format('d M Y') }} • {{ $n->category }}</div>
                    </div>
                    <span class="text-xs">{!! $n->is_published ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5">Terbit</span>' : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5">Draft</span>' !!}</span>
                </div>
            @empty
                <div class="text-sm text-slate-500 py-4">Belum ada berita.</div>
            @endforelse
        </div>
    </div>
</div>
