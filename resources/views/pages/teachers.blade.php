@php use App\Models\User; $teachers = User::where('role', 'guru')->where('is_active', true)->get(); @endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-brand-600 font-semibold mb-2">Tim Pengajar</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900">Guru <span class="text-brand-500">Profesional</span></h1>
        </div>
    </section>
    <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ($teachers as $t)
                <div class="card text-center">
                    <img src="{{ $t->photoUrl() }}" alt="{{ $t->name }}" class="h-24 w-24 mx-auto rounded-full object-cover ring-2 ring-brand-100 mb-3">
                    <div class="font-semibold text-slate-900">{{ $t->name }}</div>
                    <div class="text-xs text-slate-500">{{ $t->position }}</div>
                    @if($t->bio)<p class="mt-2 text-xs text-slate-500">{{ Str::limit($t->bio, 100) }}</p>@endif
                    <div class="flex justify-center gap-2 mt-3">
                        @if($t->instagram)<a href="{{ $t->instagram }}" class="h-7 w-7 rounded-full bg-slate-100 hover:bg-brand-100 flex items-center justify-center text-xs">IG</a>@endif
                        @if($t->facebook)<a href="{{ $t->facebook }}" class="h-7 w-7 rounded-full bg-slate-100 hover:bg-brand-100 flex items-center justify-center text-xs">FB</a>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layouts.public>
