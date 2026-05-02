@php use App\Models\Program; $programs = Program::where('is_active', true)->orderBy('order')->get(); @endphp
<x-layouts.public>
    <section class="py-8 animate-fade-up">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto liquid-glass rounded-[2rem] p-8 sm:p-12 border border-white/80 shadow-sm">
                <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-4 border border-white/60 shadow-sm">
                    Program Sekolah
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 drop-shadow-sm">Program <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-brand-700">Unggulan</span></h1>
            </div>
        </div>
    </section>
    <section class="py-12 animate-fade-up delay-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($programs as $p)
                <a href="{{ route('programs.show', $p->slug) }}" class="liquid-glass p-8 rounded-[2rem] border border-white/80 shadow-sm apple-transition hover:-translate-y-2 hover:shadow-xl group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 shadow-sm text-brand-600 mb-6 group-hover:bg-brand-500 group-hover:text-white transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-xl mb-3">{{ $p->title }}</h3>
                        <p class="text-[13px] font-medium text-slate-600 line-clamp-3 leading-relaxed">{{ $p->short_description }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.public>
