@php
    $hex = \App\Models\Setting::get('brand_color', '#65ad36');
    $palette = \App\Support\ColorPalette::fromHex($hex);
@endphp
<style>
    :root {
        --color-brand-50: {{ $palette[50] }};
        --color-brand-100: {{ $palette[100] }};
        --color-brand-200: {{ $palette[200] }};
        --color-brand-300: {{ $palette[300] }};
        --color-brand-400: {{ $palette[400] }};
        --color-brand-500: {{ $palette[500] }};
        --color-brand-600: {{ $palette[600] }};
        --color-brand-700: {{ $palette[700] }};
        --color-brand-800: {{ $palette[800] }};
        --color-brand-900: {{ $palette[900] }};
        --color-brand-950: {{ $palette[950] }};
    }
</style>
