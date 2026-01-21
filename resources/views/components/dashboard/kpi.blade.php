@props([
    'label',
    'value' => 0,
    'type' => 'default' // success | error | info
])

@php
    $colors = [
        'success' => 'border-emerald-300 bg-emerald-50 text-emerald-700 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
        'error'   => 'border-rose-300 bg-rose-50 text-rose-700 dark:border-rose-700 dark:bg-rose-950 dark:text-rose-300',
        'info'    => 'border-blue-300 bg-blue-50 text-blue-700 dark:border-blue-700 dark:bg-blue-950 dark:text-blue-300',
        'default' => 'border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100',
    ];
@endphp

<div class="rounded-lg border p-3 {{ $colors[$type] ?? $colors['default'] }}">
    <div class="text-[11px] font-medium opacity-80">{{ $label }}</div>
    <div class="mt-1 text-lg font-semibold">{{ $value }}</div>
</div>
