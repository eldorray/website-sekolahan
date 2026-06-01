@php
    $activeEventTheme = $eventTheme ?? null;
    $hex = $activeEventTheme?->primary_color ?: \App\Models\Setting::get('brand_color', '#65ad36');
    $palette = \App\Support\ColorPalette::fromHex($hex);
    $eventSecondary = $activeEventTheme?->secondary_color ?? '#ffffff';
    $eventAccent = $activeEventTheme?->accent_color ?? $palette[500];
    $eventBackground = $activeEventTheme?->background_color ?? '#f8fafc';
    $eventText = $activeEventTheme?->text_color ?? '#0f172a';
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
        --event-primary: {{ $palette[500] }};
        --event-primary-dark: {{ $palette[700] }};
        --event-primary-soft: {{ $palette[100] }};
        --event-secondary: {{ $eventSecondary }};
        --event-accent: {{ $eventAccent }};
        --event-background: {{ $eventBackground }};
        --event-text: {{ $eventText }};
    }
</style>
