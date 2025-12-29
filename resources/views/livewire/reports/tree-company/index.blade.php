<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ __('navbar.reports') }}
                / {{ __('navbar.company') }}
                / <a href="{{ route('reports.companies.tree') }}" class=" font-semibold text-indigo-600 group-hover:text-indigo-700 dark:text-indigo-400 dark:group-hover:text-indigo-300"> {{ __('navbar.company_tree') }} </a>
                @if(isset($companies[0]))
                    / {{ __('reports.tree') }}:  {{ $companies[0]['company']['commercial_name'] ?? $companies[0]['company']['name'] }}
                @endif
            </h1>
        </div>
    </x-slot>

    <div class="space-y-6 py-12">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        <th class="px-4 py-3 w-[60px]">{{ __('company.lvl') }}</th>
                        <th class="px-4 py-3 w-[50%] md:w-[40%] lg:w-[34%]">{{ __('company.name') }}</th>
                        <th class="px-4 py-3 w-[25%] md:w-[24%] lg:w-[22%]">{{ __('reports.commercial_name') }}</th>
                        <th class="px-4 py-3 w-[25%] md:w-[24%] lg:w-[22%]">{{ __('reports.company_hold') }}</th>
                        <th class="px-4 py-3 w-[120px]">{{ __('company.holding') }}</th>
                        <th class="px-4 py-3 w-[110px]">{{ __('reports.status') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($companies as $company)
                        @if($company->levels === 2)
                            <tr aria-hidden="true">
                                <td colspan="6" class="px-4 py-3 bg-gray-100 dark:bg-gray-800/60">
                                    <div class="h-3"></div>
                                </td>
                            </tr>
                        @endif
                        <tr
                            wire:key="company-tree-row-{{ $company->id }}"
                            class="
                                    text-sm text-gray-800 dark:text-gray-100
                                    even:bg-gray-50 dark:even:bg-gray-900/40
                                    hover:bg-gray-100 dark:hover:bg-gray-800
                                    focus-within:bg-indigo-50 dark:focus-within:bg-indigo-950/40
                                    transition-colors
                                "
                        >
                            <td class="px-4 py-3">{{ $company->levels }}</td>
                            <td class="px-4 py-3">

                                <div class="flex items-center min-w-0">
                                    <span class="shrink-0"></span>
                                    <span class="truncate" title="{{ $company->company->name }}">
                                        {{ $company->company->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-3" class="truncate" title="{{ $company->company->commercial_name }}">
                                {{ $company->company->commercial_name }}
                            </td>
                                @if($company->levels === 1)
                                    <td class="px-4 py-3"></td>
                                @else
                                    <td class="px-4 py-3" class="truncate" title="{{ $company?->companyParent?->name }}">
                                        {{ $company?->companyParent?->name }}
                                    </td>
                                @endif
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{
                                        $company->holding
                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                            : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200'
                                }}">
                                    {{ $company->holding ? __('company.controller') : __('company.controlled') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                                    {{ $company->company->status ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                                       : 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200' }}">
                                    {{ $company->company->status ? __('reports.active') : __('reports.inactive') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('reports.no_results_found') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
