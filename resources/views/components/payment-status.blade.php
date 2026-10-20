@props(['status', 'label' => null])
@php
    [$text, $colors] = match ($status) {
        'approved' => ['Disetujui', 'bg-emerald-50 text-emerald-800 ring-emerald-600/15 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20'],
        'paid' => ['Lunas', 'bg-emerald-50 text-emerald-800 ring-emerald-600/15 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20'],
        'pending_review' => ['Menunggu verifikasi', 'bg-amber-50 text-amber-800 ring-amber-600/15 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/20'],
        'rejected' => ['Ditolak', 'bg-red-50 text-red-800 ring-red-600/15 dark:bg-red-400/10 dark:text-red-300 dark:ring-red-400/20'],
        default => ['Perlu pemeriksaan', 'bg-slate-100 text-slate-700 ring-slate-500/15 dark:bg-slate-700 dark:text-slate-200 dark:ring-slate-500/20'],
    };
@endphp
<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset', $colors]) }}>
    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
    {{ $label ?? $text }}
</span>
