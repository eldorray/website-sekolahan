@php
    use App\Models\Setting;
    use App\Models\Program;
    use App\Models\News;
    use App\Models\User;
    $programs = Program::where('is_active', true)->orderBy('order')->take(8)->get();
    $news = News::where('is_published', true)->orderByDesc('published_at')->take(3)->get();
    $teachers = User::where('role', 'guru')->where('is_active', true)->take(8)->get();
@endphp

<x-layouts.public>
    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute -top-20 -left-20 h-80 w-80 rounded-full bg-brand-100/60 blur-3xl"></div>
            <div class="absolute top-40 right-0 h-72 w-72 rounded-full bg-brand-200/50 blur-3xl"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <p class="text-brand-600 font-semibold mb-3">{{ Setting::get('hero_welcome') }}</p>
                    <h1 class="text-5xl sm:text-6xl font-extrabold leading-[1.05] text-slate-900">
                        {{ Setting::get('hero_title_1') }}<br>
                        <span class="text-brand-500">{{ Setting::get('hero_title_2') }}</span>
                    </h1>
                    <div class="mt-5 text-lg text-slate-600 max-w-md prose prose-sm max-w-none">{!! Setting::get('hero_subtitle') !!}</div>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('about') }}" class="btn-primary">
                            Kenali Kami
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                        </a>
                        <a href="#video" class="btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M8 5v14l11-7z" /></svg>
                            Tonton Video
                        </a>
                    </div>
                    <div class="mt-10 grid grid-cols-3 gap-3 max-w-lg">
                        @foreach ([
                            ['label' => 'Siswa Aktif', 'value' => Setting::get('stat_students')],
                            ['label' => 'Guru Profesional', 'value' => Setting::get('stat_teachers')],
                            ['label' => 'Tahun Berpengalaman', 'value' => Setting::get('stat_years')],
                        ] as $stat)
                            <div class="rounded-2xl bg-white p-4 ring-1 ring-slate-100 shadow-sm">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-100 text-brand-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-slate-900">{{ $stat['value'] }}</div>
                                        <div class="text-[11px] text-slate-500 leading-tight">{{ $stat['label'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    <div class="aspect-[5/4] rounded-3xl overflow-hidden shadow-xl">
                        <img src="{{ Setting::imageUrl('hero_image') ?? 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=900&auto=format&fit=crop' }}" alt="{{ Setting::get('school_name') }}" class="h-full w-full object-cover">
                    </div>
                    <div class="absolute -bottom-6 -left-6 hidden sm:block w-64 rounded-2xl bg-white p-5 shadow-xl ring-1 ring-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 text-white font-bold">A</div>
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Sekolah Modern untuk</div>
                                <div class="text-sm font-bold text-brand-600">Generasi Hebat</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- VISI MISI --}}
    <section class="py-14 bg-slate-50/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold mb-6 text-slate-900 text-center">Visi & <span class="text-brand-500">Misi</span></h2>
            <div class="grid sm:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="card">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-600 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Visi</h3>
                    <div class="prose prose-sm max-w-none text-slate-600">{!! Setting::get('visi') !!}</div>
                </div>
                <div class="card">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-600 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-2">Misi</h3>
                    <div class="prose prose-sm max-w-none text-slate-600">{!! Setting::get('misi') !!}</div>
                </div>
            </div>
        </div>
    </section>

    {{-- PROGRAM SEKOLAH --}}
    <section class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <h2 class="text-3xl font-bold text-slate-900">Program <span class="text-brand-500">Sekolah</span></h2>
                <a href="{{ route('programs.index') }}" class="btn-ghost">Lihat Semua →</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($programs->take(4) as $p)
                    <a href="{{ route('programs.show', $p->slug) }}" class="card group hover:ring-brand-200 transition">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-brand-600 mb-4 group-hover:bg-brand-500 group-hover:text-white transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 mb-1.5">{{ $p->title }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $p->short_description }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- BERITA --}}
    <section class="py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <h2 class="text-3xl font-bold text-slate-900">Berita & <span class="text-brand-500">Artikel</span></h2>
                <a href="{{ route('news.index') }}" class="btn-ghost">Lihat Semua →</a>
            </div>
            <div class="grid lg:grid-cols-4 gap-5">
                @foreach ($news as $item)
                    <a href="{{ route('news.show', $item->slug) }}" class="group">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-3 relative">
                            <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="h-full w-full object-cover group-hover:scale-105 transition">
                            <span class="absolute top-3 left-3 rounded-md bg-brand-500 px-2 py-1 text-[10px] font-bold text-white">{{ $item->category }}</span>
                        </div>
                        <div class="text-xs text-slate-500 mb-1">{{ $item->published_at?->translatedFormat('d M Y') }}</div>
                        <h3 class="font-bold text-slate-900 leading-snug group-hover:text-brand-600 transition">{{ $item->title }}</h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $item->excerpt }}</p>
                    </a>
                @endforeach

                {{-- PPDB Card --}}
                <div class="rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 p-6 text-white relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-bold leading-tight">Penerimaan Peserta Didik Baru {{ Setting::get('ppdb_year') }}</h3>
                        <p class="text-sm mt-3 text-brand-50">Bergabunglah bersama kami dan wujudkan masa depan terbaik untuk putra-putri Anda.</p>
                    </div>
                    <a href="{{ route('ppdb.create') }}" class="mt-5 inline-flex items-center gap-2 self-start rounded-full bg-slate-900 px-5 py-2.5 text-sm font-semibold hover:bg-slate-800 transition">
                        Daftar Sekarang
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- GURU --}}
    <section class="py-14 bg-slate-50/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-6">
                <h2 class="text-3xl font-bold text-slate-900">Guru <span class="text-brand-500">Profesional</span></h2>
                <a href="{{ route('teachers.index') }}" class="btn-ghost">Lihat Semua →</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($teachers->take(4) as $t)
                    <div class="card flex items-center gap-4">
                        <img src="{{ $t->photoUrl() }}" alt="{{ $t->name }}" class="h-16 w-16 rounded-full object-cover ring-2 ring-brand-100">
                        <div class="min-w-0">
                            <div class="font-semibold text-slate-900 truncate">{{ $t->name }}</div>
                            <div class="text-xs text-slate-500">{{ $t->position }}</div>
                            <div class="flex gap-1.5 mt-2">
                                @if($t->instagram)<a href="{{ $t->instagram }}" class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 hover:bg-brand-100">IG</a>@endif
                                @if($t->facebook)<a href="{{ $t->facebook }}" class="h-6 w-6 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-500 hover:bg-brand-100">FB</a>@endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA Visit --}}
    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gradient-to-r from-brand-300 to-brand-500 p-6 lg:p-8 flex flex-col lg:flex-row items-center gap-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white/30 text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                </div>
                <div class="flex-1 text-white">
                    <div class="text-xl font-bold">Jadwalkan Kunjungan Sekolah</div>
                    <div class="text-sm text-white/90 mt-1">Datang dan rasakan langsung lingkungan belajar yang islami dan modern.</div>
                </div>
                <a href="{{ route('contact') }}#visit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition">Atur Janji →</a>
            </div>
        </div>
    </section>
</x-layouts.public>
