<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }} / {{ __('navbar.company') }} / {{ __('navbar.consult') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">

    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3 w-full">
            <input type="text"
                   wire:model.live="search"
                   placeholder="{{ __('reports.search_here') }}"
                   class="w-full max-w-md rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                      focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
        </div>
        <a href="{{ route('settings.companies.create') }}"
           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            {{ __('buttons.new') }}
        </a>
    </div>


    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                    <th class="px-4 py-3">{{ __('reports.name') }}</th>
                    <th class="px-4 py-3">{{ __('reports.commercial_name') }}</th>
                    <th class="px-4 py-3">{{ __('reports.cnpj') }}</th>
                    <th class="px-4 py-3">{{ __('reports.publicity_trade') }}</th>
                    <th class="px-4 py-3">{{ __('reports.status') }}</th>
                    <th class="px-4 py-3 w-24">{{ __('reports.actions') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($companies as $company)
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
                    <td class="px-4 py-3">{{ $company->name }}</td>
                    <td class="px-4 py-3">{{ $company->commercial_name }}</td>
                    <td class="px-4 py-3">{{ $company->cnpj }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                            {{ $company->publicity_trade ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                               : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                            {{ $company->publicity_trade ? __('reports.active') : __('reports.inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                            {{ $company->status ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                               : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                            {{ $company->status ? __('reports.active') : __('reports.inactive') }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('settings.companies.edit', $company) }}"
                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                            {{ __('buttons.edit') }}
                        </a>
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

    <div>
        {{ $companies->links() }}
    </div>
</div>
