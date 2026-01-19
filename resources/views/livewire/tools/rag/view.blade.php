<x-slot name="header">
    <div class="flex items-center justify-between gap-3">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $result['name'] }}
        </h2>

        <a
            href="{{ route('rag.download', ['files' => $files]) }}"
            class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700"
        >{{ __('buttons.download') }}</a>
    </div>
</x-slot>

<div class="py-6 space-y-5">
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="max-h-[calc(100vh-260px)] overflow-auto">
            <table class="min-w-full text-sm table-fixed">
                <colgroup>
                    <col class="w-[170px]">
                    <col class="w-[140px]">
                    <col class="w-[420px]">
                    @foreach($result['aClosing'] as $period => $sum)
                        <col class="w-[140px]">
                    @endforeach
                </colgroup>

                <thead class="sticky top-0 z-20 bg-gray-50 dark:bg-gray-950">
                    <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300">
                        <th class="px-3 py-2" colspan="3">
                            <div class="flex items-center gap-2">
                                <span class="text-gray-500 dark:text-gray-400">{{ __('labels.balance_validation') }}</span>
                                <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                    {{ __('labels.sum_of_closing_balance')  }}
                                </span>
                            </div>
                        </th>

                        @foreach($result['aClosing'] as $period => $sum)
                            @php
                                $isZero = abs((float)$sum) < 0.05;
                            @endphp
                            <th class="px-3 py-2 text-right">
                                <span class="font-mono font-semibold
                                    {{ $isZero
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ number_format((float)$sum, 2, ',', '.') }}
                                </span>
                            </th>
                        @endforeach
                    </tr>

                    <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-800">
                        <th class="px-3 py-2">{{ __('labels.account') }}</th>
                        <th class="px-3 py-2">{{ __('labels.clean_account') }}</th>
                        <th class="px-3 py-2">{{ __('labels.description') }}</th>

                        @foreach($result['aClosing'] as $period => $sum)
                            <th class="px-3 py-2 text-right">{{ $period }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($result['response'] as $account => $value)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/40">
                            <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                <span class="font-mono">{{ $account }}</span>
                            </td>

                            <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                <span class="font-mono">{{ $value['clear_account'] }}</span>
                            </td>

                            <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-200">
                                <div class="truncate max-w-[420px]">{{ $value['description'] }}</div>
                            </td>

                            @foreach($result['aClosing'] as $period => $sum)
                                @php
                                    $val = $value['balance'][$period] ?? null;
                                @endphp
                                <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-200 text-right font-mono">
                                    {{ is_null($val) ? '—' : number_format((float)$val, 2, ',', '.') }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 3 + count($result['aClosing']) }}" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('labels.no_lines_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
