@props(['name' => null, 'tagline' => null, 'dark' => false, 'imageKey' => 'school_logo'])
@php
    use App\Models\Setting;
    $name = $name ?? Setting::get('school_name', 'Sekolah');
    $tagline = $tagline ?? Setting::get('tagline', '');
    $imageUrl = Setting::imageUrl($imageKey);
@endphp
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    @if ($imageUrl)
        <img src="{{ $imageUrl }}" alt="{{ $name }}" class="h-11 w-11 rounded-xl object-contain bg-white ring-1 ring-slate-100 p-1">
    @else
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-500 text-white shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V12m0 0L4.5 7.5M12 12l7.5-4.5M12 3l7.5 4.5v9L12 21l-7.5-4.5v-9L12 3z" />
            </svg>
        </div>
    @endif
    <div class="leading-tight">
        <div class="font-bold {{ $dark ? 'text-white' : 'text-slate-900' }}">{{ $name }}</div>
        @if($tagline)<div class="text-xs {{ $dark ? 'text-slate-300' : 'text-slate-500' }}">{{ $tagline }}</div>@endif
    </div>
</a>
