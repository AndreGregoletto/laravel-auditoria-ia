<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.reports') }} / {{ __('company.name') }} / {{ __('navbar.company_tree') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full">
            <input type="text"
                   wire:model.live.debounce.400ms="search"
                   placeholder="{{ __('reports.search_here') }}"
                   class="w-full max-w-md rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                          focus:border-indigo-500 focus:ring-indigo-500
                          dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($aCompanyTree as $company)
            <div
                class="group block rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition
                      hover:shadow-md hover:border-indigo-300
                      dark:border-gray-800 dark:bg-gray-900 dark:hover:border-indigo-700">

                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $company['commercial_name'] ?? $company['name'] }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-bold">{{ __('company.name') }} -</span> {{ $company['name'] }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-bold">{{ __('reports.commercial_name') }} -</span> {{ $company['commercial_name'] }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-bold">{{ __('reports.cnpj') }} -</span> {{ $company['cnpj'] }}
                        </p>
                    </div>

                    <span class="shrink-0 inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                        {{ $company['status']
                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200'
                        }}">
                        {{ $company['status'] ? __('reports.active') : __('reports.inactive') }}
                    </span>
                </div>

                <div class="mt-3 h-px bg-gray-100 dark:bg-gray-800"></div>

                <div class="mt-3 flex items-center justify-between">
                    <a href="{{ route('reports.companies.index_tree_company', $company['id']) }}"
                       class="text-xs font-semibold text-indigo-600 group-hover:text-indigo-700 dark:text-indigo-400 dark:group-hover:text-indigo-300">
                        {{ __('reports.see_report') }}
                    </a>

                    <a href="{{ route('settings.companies_tree.organizational_chart.index', $company['id']) }}"
                       target="_blank"
                       class="text-xs font-semibold text-indigo-600 group-hover:text-indigo-700 dark:text-indigo-400 dark:group-hover:text-indigo-300">
                        {{ __('reports.org_chart') }} →
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                {{ __('reports.no_results_found') }}
            </div>
        @endforelse
    </div>
</div>
