@php use App\Models\Setting; @endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-brand-600 font-semibold mb-2">Tentang Kami</p>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900">{{ Setting::get('school_name') }}</h1>
                <div class="mt-4 text-slate-600 prose prose-sm max-w-none mx-auto">{!! Setting::get('hero_subtitle') !!}</div>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-10 items-center">
            <div class="aspect-[5/4] rounded-3xl overflow-hidden">
                <img src="https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=900&auto=format&fit=crop" class="h-full w-full object-cover" alt="">
            </div>
            <div>
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Sejarah Singkat</h2>
                <div class="prose prose-slate max-w-none">{!! Setting::get('about_history') !!}</div>
                <div class="grid grid-cols-3 gap-3 mt-6">
                    <div class="card text-center">
                        <div class="text-2xl font-bold text-brand-600">{{ Setting::get('stat_students') }}</div>
                        <div class="text-xs text-slate-500">Siswa Aktif</div>
                    </div>
                    <div class="card text-center">
                        <div class="text-2xl font-bold text-brand-600">{{ Setting::get('stat_teachers') }}</div>
                        <div class="text-xs text-slate-500">Guru</div>
                    </div>
                    <div class="card text-center">
                        <div class="text-2xl font-bold text-brand-600">{{ Setting::get('stat_years') }}</div>
                        <div class="text-xs text-slate-500">Tahun</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 bg-slate-50/40">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-6">
            <div class="card">
                <h3 class="text-xl font-bold mb-3 text-slate-900">Visi</h3>
                <div class="prose prose-slate max-w-none">{!! Setting::get('visi') !!}</div>
            </div>
            <div class="card">
                <h3 class="text-xl font-bold mb-3 text-slate-900">Misi</h3>
                <div class="prose prose-slate max-w-none">{!! Setting::get('misi') !!}</div>
            </div>
        </div>
    </section>
</x-layouts.public>
