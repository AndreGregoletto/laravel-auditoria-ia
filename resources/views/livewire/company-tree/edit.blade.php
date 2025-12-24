<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ __('navbar.settings') }}
                / {{ __('navbar.company_tree') }}
                / {{ __('navbar.edit') }}
                @if(isset($companies[0]))
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
                    {{--                @dd($company)--}}
                    <tr class="text-sm text-gray-800 dark:text-gray-100">
                        <td class="px-4 py-3">{{ $company->company->name }}</td>
                        <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                            {{ $company->holding ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                               : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                            {{ $company->holding ? __('company.controller') : __('company.controlled') }}
                        </span>
                        </td>
                        <td class="px-4 py-3">{{ $company->levels }}</td>
                        <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                            {{ $company->company->status ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                               : 'bg-red-100 text-red-700 dark:bg-red-800 dark:text-red-200' }}">
                            {{ $company->company->status ? __('reports.active') : __('reports.inactive') }}
                        </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    wire:click="openAddChild({{ $company->company_id }})"
                                    class="inline-flex items-center justify-center rounded-lg p-2 text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800
                                    dark:text-indigo-400 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-300"
                                    title="{{ __('buttons.add') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    wire:click="confirmToggleStatus({{ $company->company_id }})"
                                    class="inline-flex items-center justify-center rounded-lg p-2
                                    {{ $company->status ? 'text-emerald-600 hover:bg-emerald-50 hover:text-emerald-800 dark:text-emerald-400 dark:hover:bg-emerald-950/30'
                                      : 'text-rose-600 hover:bg-rose-50 hover:text-rose-800 dark:text-rose-400 dark:hover:bg-rose-950/30' }}"
                                    title="{{ $company->status ? __('reports.active') : __('reports.inactive') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="9" />
                                    </svg>
                                </button>
                            </div>
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

    {{-- Modal: adicionar empresa filha --}}
    @if($showAddChildModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="closeAddChild"></div>

            <div class="relative w-full max-w-lg rounded-xl bg-white p-6 shadow-lg dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('company.add_new_parent_to') }}
                </h2>

                <div class="mt-4 space-y-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('company.select_child_company') }}
                    </label>

                    <select wire:model="childCompanyId"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-950">
                        <option value="">{{ __('labels.select') }}</option>
                        @foreach($availableCompanies as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>

                    @error('childCompanyId')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            wire:click="closeAddChild"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100
                               dark:text-gray-200 dark:hover:bg-gray-800">
                        {{ __('buttons.cancel') }}
                    </button>

                    <button type="button"
                            wire:click="storeChild"
                            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        {{ __('buttons.add') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showToggleStatusModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="closeToggleStatus"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('labels.confirm_action') }}
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('labels.are_u_sure_u_want') }} {{ $nextStatus ? __('labels.activate') : __('labels.inactivate') }} {{ __('labels.this_company_tree') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            wire:click="closeToggleStatus"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100
                               dark:text-gray-200 dark:hover:bg-gray-800">
                        {{ __('buttons.cancel') }}
                    </button>

                    <button type="button"
                            wire:click="toggleStatusConfirmed"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-white
                               {{ $nextStatus ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }}">
                        {{ __('buttons.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
