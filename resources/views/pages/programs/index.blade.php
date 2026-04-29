@php use App\Models\Program; $programs = Program::where('is_active', true)->orderBy('order')->get(); @endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-brand-600 font-semibold mb-2">Program Sekolah</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900">Program <span class="text-brand-500">Unggulan</span></h1>
        </div>
    </section>
    <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($programs as $p)
                <a href="{{ route('programs.show', $p->slug) }}" class="card hover:ring-brand-200 transition group">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-100 text-brand-600 mb-4 group-hover:bg-brand-500 group-hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" /></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg mb-2">{{ $p->title }}</h3>
                    <p class="text-sm text-slate-600">{{ $p->short_description }}</p>
                </a>
            @endforeach
        </div>
    </section>
</x-layouts.public>
