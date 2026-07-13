@props(['stacked' => false])
@php $current = app()->getLocale(); @endphp
<div {{ $attributes->class([
    'inline-flex items-center rounded-full bg-white/60 border border-white/60 p-1 text-xs font-semibold',
    'w-full justify-center' => $stacked,
]) }}>
    @foreach (['id' => 'ID', 'en' => 'EN'] as $code => $label)
        <a href="{{ route('locale.switch', $code) }}"
            class="px-3 py-1.5 rounded-full transition {{ $current === $code ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}"
            aria-label="{{ __('Ganti bahasa ke :lang', ['lang' => $label]) }}"
            @if ($current === $code) aria-current="true" @endif>
            {{ $label }}
        </a>
    @endforeach
</div>
