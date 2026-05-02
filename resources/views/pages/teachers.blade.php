@php use App\Models\User; $teachers = User::where('role', 'guru')->where('is_active', true)->get(); @endphp
<x-layouts.public>
    <section class="py-8 animate-fade-up">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto liquid-glass rounded-[2rem] p-8 sm:p-12 border border-white/80 shadow-sm">
                <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-4 border border-white/60 shadow-sm">
                    Tim Pengajar
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 drop-shadow-sm">Guru <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-brand-700">Profesional</span></h1>
            </div>
        </div>
    </section>
    <section class="py-12 animate-fade-up delay-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($teachers as $t)
                <div class="liquid-glass rounded-[2rem] p-6 text-center border border-white/80 shadow-sm apple-transition hover:-translate-y-2 hover:shadow-xl group relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="relative z-10">
                        <div class="relative inline-block mb-4">
                            <img src="{{ $t->photoUrl() }}" alt="{{ $t->name }}" class="h-28 w-28 mx-auto rounded-full object-cover border-4 border-white shadow-md transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 rounded-full shadow-inner pointer-events-none"></div>
                        </div>
                        <div class="font-bold text-slate-900 text-lg group-hover:text-brand-600 transition-colors">{{ $t->name }}</div>
                        <div class="text-[12px] font-semibold text-brand-500 bg-brand-50 inline-block px-3 py-1 rounded-full mt-2 mb-3">{{ $t->position }}</div>
                        @if($t->bio)<p class="mt-1 text-[13px] text-slate-600 font-medium line-clamp-3 leading-relaxed">{{ Str::limit($t->bio, 100) }}</p>@endif
                        <div class="flex justify-center gap-3 mt-5">
                            @if($t->instagram)<a href="{{ $t->instagram }}" class="w-8 h-8 rounded-full bg-white border border-slate-100 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-sm transition-all"><span class="uppercase">IG</span></a>@endif
                            @if($t->facebook)<a href="{{ $t->facebook }}" class="w-8 h-8 rounded-full bg-white border border-slate-100 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center text-[10px] font-bold text-slate-500 shadow-sm transition-all"><span class="uppercase">FB</span></a>@endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>
