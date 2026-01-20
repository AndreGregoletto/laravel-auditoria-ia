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

@php
    $rows = [];
    foreach ($result['response'] as $account => $value) {
        $rows[] = [
            'account' => $account,
            'clean_account' => $value['clear_account'],
            'description' => $value['description'],
            'balance' => $value['balance'] ?? [],
        ];
    }

    $periods = array_keys($result['aClosing'] ?? []);
@endphp

<div
    x-data="ragTable(@js($rows), @js($periods))"
    class="py-6 space-y-4"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-[420px]">
            <input
                x-model.debounce.250ms="q"
                type="text"
                placeholder="Buscar por conta, descrição, valor..."
                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30
                       dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100"
            />
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            <span x-text="filtered.length"></span> linhas
        </div>
    </div>

    {{-- Table wrapper --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="max-h-[calc(100vh-300px)] overflow-auto">
            <table class="min-w-full text-sm table-fixed">
                <colgroup>
                    <col class="w-[170px]">
                    <col class="w-[140px]">
                    <col class="w-[420px]">
                    @foreach($periods as $p)
                        <col class="w-[140px]">
                    @endforeach
                </colgroup>

                <thead class="sticky top-0 z-20 bg-gray-50 dark:bg-gray-950">
                <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300">
                    <th class="px-3 py-2" colspan="3">
                        <div class="flex items-center gap-2">
                            <span class="text-gray-500 dark:text-gray-400">{{ __('labels.balance_validation') }}</span>
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">
                                    {{ __('labels.sum_of_closing_balance') }}
                                </span>
                        </div>
                    </th>

                    @foreach($result['aClosing'] as $period => $sum)
                        @php $isZero = abs((float)$sum) < 0.05; @endphp
                        <th class="px-3 py-2 text-right">
                                <span class="font-mono font-semibold {{ $isZero ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    {{ number_format((float)$sum, 2, ',', '.') }}
                                </span>
                        </th>
                    @endforeach
                </tr>

                <tr class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300 border-t border-gray-200 dark:border-gray-800">
                    <th class="px-3 py-2">
                        <button type="button" class="inline-flex items-center gap-1 hover:text-gray-900 dark:hover:text-white"
                                @click="toggleSort('account')">
                            {{ __('labels.account') }}
                            <span x-text="sortIcon('account')"></span>
                        </button>
                    </th>

                    <th class="px-3 py-2">
                        <button type="button" class="inline-flex items-center gap-1 hover:text-gray-900 dark:hover:text-white"
                                @click="toggleSort('clean_account')">
                            {{ __('labels.clean_account') }}
                            <span x-text="sortIcon('clean_account')"></span>
                        </button>
                    </th>

                    <th class="px-3 py-2">
                        <button type="button" class="inline-flex items-center gap-1 hover:text-gray-900 dark:hover:text-white"
                                @click="toggleSort('description')">
                            {{ __('labels.description') }}
                            <span x-text="sortIcon('description')"></span>
                        </button>
                    </th>

                    @foreach($periods as $p)
                        <th class="px-3 py-2 text-right">
                            <button type="button" class="inline-flex w-full items-center justify-end gap-1 hover:text-gray-900 dark:hover:text-white"
                                    @click="toggleSortPeriod('{{ $p }}')">
                                {{ $p }}
                                <span x-text="sortIcon('period:{{ $p }}')"></span>
                            </button>
                        </th>
                    @endforeach
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="row in sorted" :key="row.account">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-950/40">
                            <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                <span class="font-mono" x-text="row.account"></span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300">
                                <span class="font-mono" x-text="row.clean_account"></span>
                            </td>
                            <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-200">
                                <div class="truncate max-w-[420px]" x-text="row.description"></div>
                            </td>

                            @foreach($periods as $p)
                                <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-200 text-right font-mono"
                                    x-text="fmt(row.balance['{{ $p }}'])">
                                </td>
                            @endforeach
                        </tr>
                    </template>

                    <template x-if="sorted.length === 0">
                        <tr>
                            <td colspan="{{ 3 + count($periods) }}" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('labels.no_lines_found') }}
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function ragTable(rows, periods) {
        return {
            q: '',
            sortKey: 'account',
            sortDir: 'asc',

            get filtered() {
                const q = (this.q || '').toLowerCase().trim();
                if (!q) return rows;

                return rows.filter(r => {
                    const hayText =
                        (r.account ?? '') + ' ' +
                        (r.clean_account ?? '') + ' ' +
                        (r.description ?? '');

                    if (hayText.toLowerCase().includes(q)) return true;

                    for (const p of periods) {
                        const v = r.balance?.[p];
                        if (v === null || v === undefined) continue;
                        if (String(v).includes(q)) return true;
                    }
                    return false;
                });
            },

            get sorted() {
                const data = [...this.filtered];
                const dir = this.sortDir === 'asc' ? 1 : -1;

                const key = this.sortKey;

                data.sort((a,b) => {
                    if (key.startsWith('period:')) {
                        const p = key.replace('period:', '');
                        const av = a.balance?.[p];
                        const bv = b.balance?.[p];

                        const an = (av === null || av === undefined) ? -Infinity : Number(av);
                        const bn = (bv === null || bv === undefined) ? -Infinity : Number(bv);

                        return (an - bn) * dir;
                    }

                    const av = (a[key] ?? '').toString().toLowerCase();
                    const bv = (b[key] ?? '').toString().toLowerCase();
                    if (av < bv) return -1 * dir;
                    if (av > bv) return  1 * dir;
                    return 0;
                });

                return data;
            },

            toggleSort(k) {
                if (this.sortKey === k) {
                    this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortKey = k;
                    this.sortDir = 'asc';
                }
            },

            toggleSortPeriod(p) {
                const k = `period:${p}`;
                this.toggleSort(k);
            },

            sortIcon(k) {
                if (this.sortKey !== k) return '↕';
                return this.sortDir === 'asc' ? '↑' : '↓';
            },

            fmt(v) {
                if (v === null || v === undefined) return '0,00';
                // format BR
                const n = Number(v);
                return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },
        }
    }
</script>
