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
            'account'       => $account,
            'clean_account' => $value['clear_account'],
            'description'   => $value['description'],
            'balance'       => $value['balance'] ?? [],
        ];
    }

    $periods = [];
    foreach ($result['fileOrder'] as $item) {
        $periods[] = "{$item['reference_month']}/{$item['reference_year']}";
    }
@endphp

<div
    x-data="ragTable(@js($rows), @js($periods))"
    x-init="init()"
    class="py-6 space-y-4"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:w-[420px]">
            <input
                x-model.debounce.250ms="q"
                type="text"
                placeholder="{{ __('labels.search_by_account_description') }}"
                class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                       focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30
                       dark:border-gray-800 dark:bg-gray-950 dark:text-gray-100"
            />
        </div>

        <div class="flex flex-wrap items-center gap-4">
            <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 select-none">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500/30 dark:border-gray-700"
                    x-model="highlightVariations"
                />
                {{ __('labels.highlight_variations') }}
            </label>

            <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 select-none">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-rose-600 focus:ring-rose-500/30 dark:border-gray-700"
                    x-model="onlySuspicious"
                />
                {{ __('labels.show_only_suspicions') }}
            </label>

            <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 select-none">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/30 dark:border-gray-700"
                    x-model="useAbsRule"
                    @change="recalc()"
                />
                {{ __('labels.consider_absolute_value') }}
            </label>

            <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 select-none">
                <input
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/30 dark:border-gray-700"
                    x-model="usePctRule"
                    @change="recalc()"
                />
                {{ __('labels.consider_percentage_variation') }}
            </label>

            <div class="text-sm text-gray-500 dark:text-gray-400">
                <span x-text="filtered.length"></span> {{ __('labels.line') }}
            </div>
        </div>
    </div>

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
                                {{ number_format((float) $sum, 2, ',', '.') }}
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
                    <tr
                        class="hover:ring-1 hover:ring-gray-300/60 dark:hover:ring-gray-700/60"
                        :class="rowClass(row)"
                        :title="row.__alert?.reason ? (row.__alert.reason + (row.__alert.worstPeriod ? ' em ' + row.__alert.worstPeriod : '')) : ''"
                    >
                        <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300 bg-inherit">
                            <div class="flex items-center gap-2">
                                    <span class="inline-flex h-2 w-2 rounded-full"
                                          :class="rowDotClass(row)"
                                          aria-hidden="true"></span>
                                <span class="font-mono" x-text="row.account"></span>
                            </div>
                        </td>

                        <td class="px-3 py-2 text-xs text-gray-600 dark:text-gray-300 bg-inherit">
                            <span class="font-mono" x-text="row.clean_account"></span>
                        </td>

                        <td class="px-3 py-2 text-xs text-gray-700 dark:text-gray-200 bg-inherit">
                            <div class="truncate max-w-[420px]" x-text="row.description"></div>
                        </td>

                        @foreach($periods as $p)
                            <td
                                class="px-3 py-2 text-xs text-gray-700 dark:text-gray-200 text-right font-mono bg-inherit"
                                :class="cellClass(row, '{{ $p }}')"
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

            highlightVariations: true,
            onlySuspicious: false,

            useAbsRule: true,
            usePctRule: true,

            absFloor: 1000,
            absFloorSmall: 200,
            pctThreshold: 0.15, // 15%

            rowBorderByLevel: [
                '',
                'border-l-2 border-rose-200/80 dark:border-rose-900/60',
                'border-l-2 border-rose-300/80 dark:border-rose-800/70',
                'border-l-4 border-rose-400/80 dark:border-rose-700/70',
                'border-l-4 border-rose-500/90 dark:border-rose-600/80',
            ],

            dotByLevel: [
                'bg-transparent',
                'bg-rose-200 dark:bg-rose-800',
                'bg-rose-300 dark:bg-rose-700',
                'bg-rose-400 dark:bg-rose-600',
                'bg-rose-500 dark:bg-rose-500',
            ],

            cellSuspect: 'bg-rose-100/70 dark:bg-rose-950/30 text-rose-800 dark:text-rose-200 font-semibold',
            cellWorst:   'bg-rose-200/80 dark:bg-rose-950/40 text-rose-900 dark:text-rose-100 font-bold underline decoration-rose-400/50',

            init() {
                this.decorateRows();
            },

            recalc() {
                this.decorateRows();
            },

            decorateRows() {
                for (const r of rows) {
                    const values = periods.map(p => Number(r.balance?.[p] ?? 0));
                    r.__alert = this.analyze(values);
                }
            },

            median(nums) {
                const a = nums.filter(n => Number.isFinite(n)).slice().sort((x,y) => x-y);
                if (!a.length) return 0;
                const mid = Math.floor(a.length / 2);
                return a.length % 2 ? a[mid] : (a[mid-1] + a[mid]) / 2;
            },

            analyze(values) {
                const n = values.length;
                if (n < 2) {
                    return { suspicious:false, level:0, worstPeriod:null, suspiciousPeriods:new Set(), reason:null };
                }

                const absVals = values.map(v => Math.abs(v));
                const base = this.median(absVals);

                const absFloor = base < 2000 ? this.absFloorSmall : this.absFloor;

                const deltas = [];
                for (let i = 1; i < n; i++) {
                    deltas.push(Math.abs(values[i] - values[i-1]));
                }

                const nz = deltas.filter(d => d > 0.000001);
                const medDelta = this.median(nz);
                const deviations = nz.map(d => Math.abs(d - medDelta));
                const mad = this.median(deviations);

                const k = 3.0;

                const suspiciousPeriods = new Set();
                let worstScore = 0;
                let worstIdx = null;

                for (let i = 1; i < n; i++) {
                    const prev = values[i-1];
                    const curr = values[i];
                    const delta = Math.abs(curr - prev);

                    const pct = base > 0 ? (delta / base) : 0;

                    const scoreAbs = absFloor > 0 ? Math.min(1, delta / absFloor) : 0;
                    const scorePct = this.pctThreshold > 0 ? Math.min(1, pct / this.pctThreshold) : 0;

                    let materialScore = 0;
                    if (this.useAbsRule && this.usePctRule) materialScore = Math.max(scoreAbs, scorePct);
                    else if (this.useAbsRule) materialScore = scoreAbs;
                    else if (this.usePctRule) materialScore = scorePct;
                    else materialScore = 0;

                    if (materialScore <= 0) continue;

                    let outlierScore = 0;
                    if (mad > 0) {
                        outlierScore = Math.abs(delta - medDelta) / mad;
                    } else {
                        outlierScore = medDelta > 0 ? (delta / medDelta) : 0;
                    }

                    const isOutlier =
                        (mad > 0 && outlierScore >= k && materialScore >= 0.6) ||
                        (mad === 0 && materialScore >= 0.85);

                    if (isOutlier) {
                        suspiciousPeriods.add(periods[i]);

                        const severity = Math.min(1, Math.max(materialScore, outlierScore / (k * 2)));

                        if (severity > worstScore) {
                            worstScore = severity;
                            worstIdx = i;
                        }
                    }
                }

                let level = 0;
                if (worstScore >= 0.90) level = 4;
                else if (worstScore >= 0.70) level = 3;
                else if (worstScore >= 0.55) level = 2;
                else if (worstScore >= 0.40) level = 1;

                const suspicious = level > 0;
                const worstPeriod = (worstIdx !== null && suspicious) ? periods[worstIdx] : null;
                const titleLabel = "@php echo __('labels.non_standard_relevant_variation') @endphp";
                const reason = suspicious
                    ? `${titleLabel} ~ ${base.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2})})`
                    : null;

                return { suspicious, level, worstPeriod, suspiciousPeriods, reason };
            },

            rowClass(row) {
                if (!this.highlightVariations) return '';
                const lvl = row.__alert?.level ?? 0;
                return this.rowBorderByLevel[lvl] ?? '';
            },

            rowDotClass(row) {
                if (!this.highlightVariations) return 'bg-transparent';
                const lvl = row.__alert?.level ?? 0;
                return this.dotByLevel[lvl] ?? 'bg-transparent';
            },

            cellClass(row, period) {
                if (!this.highlightVariations) return '';
                const a = row.__alert;
                if (!a?.suspicious) return '';

                if (a.worstPeriod === period) return this.cellWorst;
                if (a.suspiciousPeriods?.has(period)) return this.cellSuspect;
                return '';
            },

            get filtered() {
                const q = (this.q || '').toLowerCase().trim();
                let baseRows = rows;

                if (this.onlySuspicious) {
                    baseRows = baseRows.filter(r => r.__alert?.suspicious);
                }

                if (!q) return baseRows;

                return baseRows.filter(r => {
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
                this.toggleSort(`period:${p}`);
            },

            sortIcon(k) {
                if (this.sortKey !== k) return '↕';
                return this.sortDir === 'asc' ? '↑' : '↓';
            },

            fmt(v) {
                if (v === null || v === undefined) return '0,00';
                const n = Number(v);
                return n.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },
        }
    }
</script>
