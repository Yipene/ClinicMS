@props(['label', 'value', 'hint' => null, 'color' => 'teal'])

@php
    $colors = [
        'teal' => 'from-teal-500 to-emerald-600',
        'blue' => 'from-blue-500 to-indigo-600',
        'amber' => 'from-amber-500 to-orange-600',
        'rose' => 'from-rose-500 to-pink-600',
    ];
    $gradient = $colors[$color] ?? $colors['teal'];
@endphp

<div class="rounded-2xl bg-white p-5 shadow-sm border border-slate-100 hover:shadow-md transition">
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-400">{{ $hint }}</p>
    @endif
    <div class="mt-3 h-1 w-12 rounded-full bg-gradient-to-r {{ $gradient }}"></div>
</div>
