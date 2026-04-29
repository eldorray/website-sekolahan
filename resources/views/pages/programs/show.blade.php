@php
    use App\Models\Program;
    $program = Program::where('slug', $slug)->where('is_active', true)->firstOrFail();
@endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
            <a href="{{ route('programs.index') }}" class="text-sm text-brand-600 hover:underline">← Semua Program</a>
            <h1 class="mt-4 text-4xl sm:text-5xl font-extrabold text-slate-900">{{ $program->title }}</h1>
            <p class="mt-3 text-slate-600">{{ $program->short_description }}</p>
        </div>
    </section>
    <section class="py-12">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 prose prose-slate">
            {!! $program->description !!}
        </div>
    </section>
</x-layouts.public>
