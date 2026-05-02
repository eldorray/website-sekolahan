@php use App\Models\Setting; @endphp
<x-layouts.public>
    <section class="py-8 animate-fade-up">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto liquid-glass rounded-[2rem] p-8 sm:p-12 border border-white/80 shadow-sm">
                <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-4 border border-white/60 shadow-sm">
                    Tentang Kami
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 mb-6 drop-shadow-sm">{{ Setting::get('school_name') }}</h1>
                <div class="text-slate-600 prose prose-sm max-w-none mx-auto font-medium">{!! Setting::get('hero_subtitle') !!}</div>
            </div>
        </div>
    </section>

    <section class="py-12 animate-fade-up delay-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-center">
            <div class="aspect-[5/4] rounded-[2rem] overflow-hidden shadow-lg border border-white/40">
                <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=900&auto=format&fit=crop" class="h-full w-full object-cover transition-transform duration-700 hover:scale-105" alt="">
            </div>
            <div class="liquid-glass rounded-[2rem] p-8 border border-white/80">
                <h2 class="text-3xl font-bold text-slate-900 mb-6 relative inline-block">
                    Sejarah Singkat
                    <div class="absolute -bottom-2 left-0 w-12 h-1 bg-brand-500 rounded-full"></div>
                </h2>
                <div class="prose prose-slate max-w-none font-medium text-slate-600 mb-8">{!! Setting::get('about_history') !!}</div>
                <div class="grid grid-cols-3 gap-4 mt-6">
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 text-center apple-transition hover:-translate-y-1 hover:shadow-md">
                        <div class="text-2xl font-bold text-brand-600">{{ Setting::get('stat_students') }}</div>
                        <div class="text-[12px] font-semibold text-slate-500 mt-1">Siswa Aktif</div>
                    </div>
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 text-center apple-transition hover:-translate-y-1 hover:shadow-md">
                        <div class="text-2xl font-bold text-brand-600">{{ Setting::get('stat_teachers') }}</div>
                        <div class="text-[12px] font-semibold text-slate-500 mt-1">Guru</div>
                    </div>
                    <div class="bg-white/50 backdrop-blur-sm p-4 rounded-[1.5rem] border border-white/60 text-center apple-transition hover:-translate-y-1 hover:shadow-md">
                        <div class="text-2xl font-bold text-brand-600">{{ Setting::get('stat_years') }}</div>
                        <div class="text-[12px] font-semibold text-slate-500 mt-1">Tahun</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 animate-fade-up delay-200">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-8">
            <div class="liquid-glass rounded-[2rem] p-8 border border-white/80 apple-transition hover:-translate-y-1 hover:shadow-lg">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-900">Visi</h3>
                <div class="prose prose-slate max-w-none font-medium text-slate-600">{!! Setting::get('visi') !!}</div>
            </div>
            <div class="liquid-glass rounded-[2rem] p-8 border border-white/80 apple-transition hover:-translate-y-1 hover:shadow-lg">
                <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center mb-6 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5" /></svg>
                </div>
                <h3 class="text-2xl font-bold mb-4 text-slate-900">Misi</h3>
                <div class="prose prose-slate max-w-none font-medium text-slate-600">{!! Setting::get('misi') !!}</div>
            </div>
        </div>
    </section>
</x-layouts.public>
