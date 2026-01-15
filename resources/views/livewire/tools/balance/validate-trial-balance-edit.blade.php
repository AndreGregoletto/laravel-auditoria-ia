<x-slot name="header">
    <div class="flex items-center justify-between gap-3">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('navbar.tools') }} / {{ __('navbar.processes') }} / {{ __('navbar.validate_bal') }} / {{ __('navbar.edit') }}
        </h2>

        <div class="text-md text-gray-500 dark:text-gray-400 hover:text-black/70">
            {{ $file->file_name }} — {{ sprintf('%02d/%04d', $file->reference_month, $file->reference_year) }}
        </div>
    </div>
</x-slot>

<div class="py-6 space-y-5">

    {{-- Filtros + stats --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">

            <div class="lg:col-span-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('labels.search') }}
                </label>
                <input
                    type="text"
                    wire:model.live.debounce.250ms="search"
                    placeholder="{{ __('reports.search_here') }}"
                    class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                           focus:border-indigo-500 focus:ring-indigo-500
                           dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                />
            </div>

            <div class="lg:col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('labels.filter') }}
                </label>
                <select
                    wire:model.live="filterIncluded"
                    class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                           focus:border-indigo-500 focus:ring-indigo-500
                           dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                >
                    <option value="all">{{ __('labels.all') }}</option>
                    <option value="included">{{ __('labels.included') }}</option>
                    <option value="excluded">{{ __('labels.excluded') }}</option>
                    <option value="undecided">{{ __('labels.undecided') }}</option>
                    <option value="redflag">{{ __('labels.red_flag') }}</option>
                </select>
            </div>

            {{-- Bulk por tamanho --}}
            <div class="lg:col-span-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('labels.bulk_action') }}
                </label>

                <div class="mt-1 grid grid-cols-12 gap-2">
                    <div class="col-span-3">
                        <input type="number"
                               wire:model.live="bulkLength"
                               placeholder="len"
                               class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                      focus:border-indigo-500 focus:ring-indigo-500
                                      dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200" />
                    </div>

                    <div class="col-span-3">
                        <select wire:model.live="bulkAction"
                                class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                       focus:border-indigo-500 focus:ring-indigo-500
                                       dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">
                            <option value="include">{{ __('labels.include_1') }}</option>
                            <option value="exclude">{{ __('labels.delete') }}</option>
                        </select>
                    </div>

                    <div class="col-span-4">
                        <input type="text"
                               wire:model.live="bulkReason"
                               placeholder="{{ __('labels.Justification_required') }}"
                               class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                      focus:border-indigo-500 focus:ring-indigo-500
                                      dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200" />
                    </div>

                    <div class="col-span-2">
                        <button type="button"
                                wire:click="applyBulkLength"
                                class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            {{ __('buttons.apply') }}
                        </button>
                    </div>
                </div>

                @error('bulkLength') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                @error('bulkReason') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
            </div>

        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-4">

            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('labels.balance_validation') }}
                </h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('labels.sum_of_closing_balance') }}
                </p>
            </div>

            @php
                $isZero = abs($totalClosingBalance) < 0.05;
            @endphp

            <div class="flex items-center gap-3">
            <span class="text-sm font-mono font-semibold
                {{ $isZero
                    ? 'text-emerald-600 dark:text-emerald-400'
                    : 'text-rose-600 dark:text-rose-400'
                }}">
                {{ number_format($totalClosingBalance, 2, ',', '.') }}
            </span>

                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                {{ $isZero
                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                    : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200'
                }}">
                {{ $isZero
                    ? __('labels.balanced')
                    : __('labels.not_balanced')
                }}
            </span>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="max-h-[calc(100vh-260px)] overflow-auto">
            <table class="min-w-full text-sm">
                <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-950">
                <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300">
                    <th class="px-3 py-2">{{ __('labels.line') }}</th>
                    <th class="px-3 py-2">{{ __('labels.account') }}</th>
                    <th class="px-3 py-2">{{ __('labels.description') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('labels.previous_balance') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('labels.debit') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('labels.credit') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('labels.monthly_activity') }}</th>
                    <th class="px-3 py-2 text-right">{{ __('labels.closing_balance') }}</th>
                    <th class="px-3 py-2 text-center">{{ __('labels.included') }}</th>
                    <th class="px-3 py-2 text-center">{{ __('labels.flag') }}</th>
                    <th class="px-3 py-2 text-center">{{ __('reports.actions') }}</th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($rows as $r)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/40">
                        <td class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $r->file_line }}
                        </td>

                        <td class="px-3 py-2 font-mono text-xs text-gray-800 dark:text-gray-200">
                            {{ $r->account }}
                        </td>

                        <td class="px-3 py-2 text-gray-800 dark:text-gray-200">
                            <div class="max-w-[420px] truncate">{{ $r->description }}</div>
                            @if($r->balance_decision_source)
                                <div class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                    {{ __('labels.source') }}: {{ __("labels.{$r->balance_decision_source}") ?? $r->balance_decision_source }}
                                </div>
                            @endif
                        </td>

                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">
                            {{ number_format((float)$r->previous_balance, 2, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">
                            {{ number_format((float)$r->debit, 2, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">
                            {{ number_format((float)$r->credit, 2, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300">
                            {{ number_format((float)$r->monthly_activity, 2, ',', '.') }}
                        </td>
                        <td class="px-3 py-2 text-right font-semibold text-gray-800 dark:text-gray-200">
                            {{ number_format((float)$r->closing_balance, 2, ',', '.') }}
                        </td>

                        {{-- Incluída --}}
                        <td class="px-3 py-2 text-center">
                            @if(is_null($r->balance_included))
                                <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                        —
                                    </span>
                            @elseif($r->balance_included)
                                <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200">
                                        {{ __('labels.yes') }}
                                    </span>
                            @else
                                <span class="inline-flex rounded-full bg-rose-100 px-2 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/40 dark:text-rose-200">
                                        {{ __('labels.no') }}
                                    </span>
                            @endif
                        </td>

                        {{-- Flag --}}
                        <td class="px-3 py-2 text-center">
                            @if($r->red_flag)
                                <span class="inline-flex rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">
                                        !
                                    </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Ações --}}
                        <td class="px-3 py-2 text-right">
                            <div class="flex justify-end gap-2">
                                <button type="button"
                                        wire:click="openDecision({{ $r->id }}, true)"
                                        class="rounded-lg bg-emerald-600 px-2 py-1 text-xs font-semibold text-white hover:bg-emerald-700">
                                    {{ __('labels.include_1') }}
                                </button>
                                <button type="button"
                                        wire:click="openDecision({{ $r->id }}, false)"
                                        class="rounded-lg bg-rose-600 px-2 py-1 text-xs font-semibold text-white hover:bg-rose-700">
                                    {{ __('labels.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('labels.no_lines_found') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal de justificativa --}}
    <div x-data="{ open: false }"
         x-on:open-modal.window="if($event.detail.id==='decision-modal') open=true"
         x-on:close-modal.window="if($event.detail.id==='decision-modal') open=false"
         x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
         style="display:none;"
    >
        <div class="w-full max-w-lg rounded-xl bg-white p-4 shadow-lg dark:bg-gray-900">
            <div class="flex items-start justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('labels.justification_for_the_decision') }}
                </h3>
                <button type="button" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-200"
                        x-on:click="open=false">
                    ✕
                </button>
            </div>

            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    {{ __('labels.Justification') }} <span class="text-rose-500">*</span>
                </label>
                <textarea
                    wire:model.live="reason"
                    rows="3"
                    class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                           focus:border-indigo-500 focus:ring-indigo-500
                           dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                    placeholder="{{ __('labels.ex_balance_1') }}"
                ></textarea>
                @error('reason') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <button type="button"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700
                               hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                        x-on:click="open=false">
                    {{ __('buttons.cancel') }}
                </button>

                <button type="button"
                        wire:click="saveDecision"
                        class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    {{ __('buttons.save_decision') }}
                </button>
            </div>
        </div>
    </div>

</div>
