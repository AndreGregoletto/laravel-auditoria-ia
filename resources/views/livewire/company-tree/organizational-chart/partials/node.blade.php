@php
    $isActive = (bool) ($node['status'] ?? false);
    $company = $node['company'];

    $publicityTrade = (int) ($company->publicity_trade ?? 0);
    $companyStatus  = (int) ($company->status ?? 0);

    $hasChildren = !empty($node['children']);
@endphp

<div class="flex flex-col items-center" x-data="{ open: true }">

    <div class="relative min-w-[200px] max-w-[230px] rounded-lg bg-white px-3 py-2 shadow-sm
                dark:bg-gray-950 border-2
                {{ $isActive ? 'border-emerald-400 dark:border-emerald-500' : 'border-rose-400 dark:border-rose-500' }}">

        @if($hasChildren)
            <button type="button"
                    class="absolute -right-2 -top-2 inline-flex h-6 w-6 items-center justify-center rounded-full border bg-white text-xs shadow
                           dark:bg-gray-900 dark:border-gray-700"
                    @click="open = !open"
                    :title="open ? 'Recolher' : 'Expandir'">
                <span x-text="open ? '−' : '+'"></span>
            </button>
        @endif

        <div class="min-w-0">

            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <div
                        class="font-semibold text-gray-900 dark:text-gray-100 truncate max-w-[180px]"
                        title="{{ $company->name }}"
                    >
                        {{ \Illuminate\Support\Str::limit($company->name, 28) }}
                    </div>
                </div>

                <span class="shrink-0 inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold
                    {{ $node['holding']
                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                        : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200'
                    }}">
                    {{ $node['holding'] ? __('company.controller') : __('company.controlled') }}
                </span>
            </div>

            <div class="mt-2 space-y-1 text-[11px] text-gray-600 dark:text-gray-300">
                <div class="flex gap-2">
                    <span class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('reports.commercial_name') }}:</span>
                    <span class="truncate" title="{{ $company->commercial_name }}">
                        {{ \Illuminate\Support\Str::limit($company->commercial_name, 30) }}
                    </span>
                </div>

                <div class="flex gap-2">
                    <span class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('reports.cnpj') }}:</span>
                    <span class="truncate">{{ $company->cnpj }}</span>
                </div>

                <div class="pt-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold
                        {{ $publicityTrade === 1
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200'
                        }}">
                        {{ __('reports.publicity_trade') }}
                    </span>

                    <span class="inline-flex items-center rounded-full px-2 py-1 text-[10px] font-semibold
                        {{ $companyStatus === 1
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                            : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200'
                        }}">
                        {{ __('reports.status') }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    @if($hasChildren)
        <div class="flex flex-col items-center" x-show="open" x-transition.opacity>

            <div class="h-6 w-[2px] bg-gray-400 dark:bg-gray-600"></div>

            <div class="relative flex items-start justify-center gap-4 md:gap-6">

                @if(count($node['children']) > 1)
                    <div class="absolute top-0 left-0 right-0 mx-4 h-[2px] bg-gray-400 dark:bg-gray-600"></div>
                @endif

                @foreach($node['children'] as $child)
                    <div class="relative flex flex-col items-center">
                        <div class="h-6 w-[2px] bg-gray-400 dark:bg-gray-600"></div>

                        @include('livewire.company-tree.organizational-chart.partials.node', ['node' => $child])
                    </div>
                @endforeach

            </div>
        </div>
    @endif

</div>
