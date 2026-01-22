@props([
    'label' => '',
    'value' => 0,
    'type'  => 'info',
    'my'    => null,
])

@php
    $value = (int) ($value ?? 0);
    $myVal = is_null($my) ? null : (int) $my;

    $pct = null;
    if (!is_null($myVal) && $value > 0) {
        $pct = (int) round(($myVal / $value) * 100);
    }

    $accent = match($type) {
        'success' => 'text-emerald-700 dark:text-emerald-300',
        'error'   => 'text-rose-700 dark:text-rose-300',
        default   => 'text-sky-700 dark:text-sky-300',
    };

    $border = match($type) {
        'success' => 'border-emerald-200 dark:border-emerald-900/50',
        'error'   => 'border-rose-200 dark:border-rose-900/50',
        default   => 'border-sky-200 dark:border-sky-900/50',
    };

    $pill = match($type) {
        'success' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-200',
        'error'   => 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-200',
        default   => 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-200',
    };
@endphp

<div class="relative rounded-xl border {{ $border }} bg-white p-4 shadow-sm dark:bg-gray-950">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 truncate">
                {{ $label }}
            </div>

            <div class="mt-2 flex items-baseline gap-2">
                <div class="text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                    {{ $value }}
                </div>

                {{-- opcional: mini indicador de status (bem discreto) --}}
                <span class="text-[11px] font-semibold {{ $accent }}">
                    ●
                </span>
            </div>

            @if(!is_null($myVal))
                <div class="mt-2 text-[12px] text-gray-500 dark:text-gray-400">
                    <span class="font-medium">{{ __('labels.my_actions') }}</span>:
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $myVal }}</span>
                    @if(!is_null($pct))
                        <span class="opacity-80">({{ $pct }}%)</span>
                    @endif
                </div>
            @endif
        </div>

        @if(!is_null($myVal))
            {{-- pill pequena no canto: não compete com o número --}}
            <div class="shrink-0">
                <span class="inline-flex items-center rounded-full px-2 py-1 text-[11px] font-medium {{ $pill }}">
                    {{ $myVal }}@if(!is_null($pct)) · {{ $pct }}%@endif
                </span>
            </div>
        @endif
    </div>
</div>
