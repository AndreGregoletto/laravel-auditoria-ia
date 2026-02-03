<x-slot name="header">
    <div class="flex items-center justify-between gap-3">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('navbar.tools') }} / {{ __('navbar.processes') }} / {{ __('navbar.validate_bal') }} / {{ __("labels.ai_Preview") }}
        </h2>

        <div class="text-md text-gray-500 dark:text-gray-400 hover:text-black/70">
            {{ $file->file_name }} — {{ sprintf('%02d/%04d', $file->reference_month, $file->reference_year) }}
        </div>
    </div>
</x-slot>

<div class="py-6 space-y-5">

    {{-- Filtros --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">

            <div class="lg:col-span-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('labels.search') }}</label>
                <input type="text" wire:model.live.debounce.250ms="search"
                       class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                              focus:border-indigo-500 focus:ring-indigo-500
                              dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                       placeholder="{{ __('reports.search_here') }}">
            </div>

            <div class="lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('labels.filter') }}</label>
                <select wire:model.live="filter"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                               focus:border-indigo-500 focus:ring-indigo-500
                               dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
                    <option value="all">{{ __("labels.all") }}</option>
                    <option value="included">{{ __('labels.suggestions_included') }}</option>
                    <option value="excluded">{{ __('labels.suggestions_excluded') }}</option>
                    <option value="changed">{{ __('labels.changed_by_the_auditor') }}</option>
                    <option value="low_confidence">{{ __('labels.low_confidence') }}</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ __('labels.min_confidence') }}</label>
                <input type="number" wire:model.live="minConfidence"
                       class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                              focus:border-indigo-500 focus:ring-indigo-500
                              dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
            </div>

            <div class="lg:col-span-3 flex items-end justify-end gap-2">
                <button type="button" wire:click="clearOverrides"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700
                               hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
                    {{ __('labels.discard_adjustments') }}
                </button>

                <button type="button" wire:click="applyPreview"
                        class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    {{ __('labels.apply_decisions') }}
                </button>
            </div>

        </div>
    </div>

    {{-- Totais (arquivo vs preview) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        @php
            $isPreviewZero = abs($previewSum) < 0.05;
            $isFileZero = abs($totalFileClosing) < 0.05;
        @endphp

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.final_balance_sum') }}</div>
                <div class="mt-1 font-mono font-semibold {{ $isFileZero ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ number_format($totalFileClosing, 2, ',', '.') }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.total_final_balance_include') }}</div>
                <div class="mt-1 font-mono font-semibold {{ $isPreviewZero ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-300' }}">
                    {{ number_format($previewSum, 2, ',', '.') }}
                </div>
            </div>

            <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.difference_preview') }}</div>
                <div class="mt-1 font-mono font-semibold {{ abs($diff) < 0.05 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                    {{ number_format($diff, 2, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="max-h-[calc(100vh-260px)] overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-950">
                <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300">
                    <th class="px-3 py-2">{{ __('labels.line') }}</th>
                    <th class="px-3 py-2">{{ __('labels.account') }}</th>
                    <th class="px-3 py-2">{{ __('labels.description') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('labels.closing_balance') }}</th>
                    <th class="px-3 py-2 text-center">{{ __('labels.ai_suggestion') }}</th>
                    <th class="px-3 py-2">{{ __('labels.rational') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('reports.actions') }}</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($rows as $r)
                    @php
                        $s = $this->suggestions[$r->id] ?? null;
                        $effective = $this->effectiveIncluded($r->id);
                        $rf = (float)($s['redflag'] ?? 0);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/40">
                        <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">{{ $r->file_line }}</td>
                        <td class="px-3 py-2 font-mono text-xs text-gray-800 dark:text-gray-200">{{ $r->account }}</td>
                        <td class="px-3 py-2 text-gray-800 dark:text-gray-200">
                            <div class="max-w-[420px] truncate">{{ $r->description }}</div>
                        </td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-800 dark:text-gray-200">
                            {{ number_format((float)$r->closing_balance, 2, ',', '.') }}
                        </td>

                        <td class="px-3 py-2 text-center">
                            @if($effective === true)
                                <span
                                    class="inline-flex rounded-full bg-emerald-100 px-2 py-1
                                        text-xs font-semibold text-emerald-700
                                        dark:bg-emerald-900/40 dark:text-emerald-200"
                                >{{ __('labels.include_1') }}</span>
                            @elseif($effective === false)
                                <span class="inline-flex rounded-full bg-rose-100 px-2 py-1
                                    text-xs font-semibold text-rose-700 dark:bg-rose-900/40
                                    dark:text-rose-200"
                                >{{ __('labels.excluded') }}</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif

                            @if($s)
                                <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ __('labels.trust') }}: {{ (int)($s['confidence'] ?? 0) }}%
                                </div>
                            @endif
                        </td>

                        <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                            <div class="max-w-[380px] truncate">
                                {{ $s['rationale'] ?? '—' }}
                            </div>
                        </td>

                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button"
                                        wire:click="toggleIncluded({{ $r->id }}, true)"
                                        class="rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-700">
                                    {{ __('labels.include_1') }}
                                </button>
                                <button type="button"
                                        wire:click="toggleIncluded({{ $r->id }}, false)"
                                        class="rounded-lg bg-rose-600 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-700">
                                    {{ __('labels.excluded') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
