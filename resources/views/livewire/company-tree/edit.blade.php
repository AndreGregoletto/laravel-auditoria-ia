<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }}
            / {{ __('navbar.company_tree') }}
            / {{ __('navbar.edit') }}
            @if(!empty($companies))
                / {{ __('reports.tree') }} {{ $companies[0]['company']['name'] }}
            @endif
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950">
            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                <th class="px-4 py-3">{{ __('company.name') }}</th>
                <th class="px-4 py-3">{{ __('company.holding') }}</th>
                <th class="px-4 py-3">{{ __('company.lvl') }}</th>
                <th class="px-4 py-3">{{ __('reports.status') }}</th>
                <th class="px-4 py-3">{{ __('reports.actions') }}</th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($companies as $company)

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
