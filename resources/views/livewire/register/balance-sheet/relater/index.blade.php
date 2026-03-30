<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }} / {{ __('navbar.register') }} / {{ __('reports.bp_classification') }} / Relacionador / {{ __('navbar.consult') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">

    <div class="flex items-center justify-between">
        <div class="grid grid-cols-3 gap-2 lg:grid-cols-6">
            <input type="text"
                wire:model.live="search"
                placeholder="{{ __('reports.search_here') }}"
                class="w-full max-w-md rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
            <select wire:model.live="idCompanyTree"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.tree') }}</option>
                @foreach($companyTree as $key => $c)
                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                @endforeach
            </select>

            <select wire:model.live="idCompany"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.company') }}</option>
                @foreach($company as $c)
                    <option value="{{ $c['id'] }}">{{ $c['commercial_name'] ?? $c['name'] }}</option>
                @endforeach
            </select>
        </div>

        <a href="{{ route('settings.register.asset-base-classification.relator.create') }}"
           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
            {{ __('buttons.new') }}
        </a>
    </div>


    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                    <th class="px-4 py-3">Conta</th>
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Código Pai</th>
                    <th class="px-4 py-3">Descrição</th>
                    <th class="px-4 py-3">Sessão</th>
                    <th class="px-4 py-3">lado</th>
                    <th class="px-4 py-3">Usuario Criador</th>
                    <th class="px-4 py-3">Usuario Alteração</th>
                    <th class="px-4 py-3">{{ __('reports.status') }}</th>
                    <th class="px-4 py-3">{{ __('reports.created_in') }}</th>
                    <th class="px-4 py-3">{{ __('reports.updated_in') }}</th>
                    <th class="px-4 py-3 w-24">{{ __('reports.actions') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($response as $res)
                    <tr
                        wire:key="company-tree-row-{{ $res->id }}"
                        class="
                            text-sm text-gray-800 dark:text-gray-100
                            even:bg-gray-50 dark:even:bg-gray-900/40
                            hover:bg-gray-100 dark:hover:bg-gray-800
                            focus-within:bg-indigo-50 dark:focus-within:bg-indigo-950/40
                            transition-colors
                        "
                    >
                        <td class="px-4 py-3">{{ $res['value'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-md font-semibold
                                {{ $res['balanceSheet']->status ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                                {{ $res['balanceSheet']['code'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $res['balanceSheet']['parent_code'] ?? '' }}</td>
                        <td class="px-4 py-3">{{ $res['balanceSheet']['name'] }}</td>
                        <td class="px-4 py-3">{{ $res['balanceSheet']['section'] }}</td>
                        <td class="px-4 py-3">{{ $res['balanceSheet']['side'] }}</td>
                        <td class="px-4 py-3">{{ $res['userCreate']['name'] }}</td>
                        <td class="px-4 py-3">{{ $res['userAlter']['name'] }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold
                                {{ $res->status ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                                   : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200' }}">
                                {{ $res->status ? __('reports.active') : __('reports.inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ optional($res->created_at)->translatedFormat('d F Y, H:i') }}</td>
                        <td class="px-4 py-3">{{ optional($res->updated_at)->translatedFormat('d F Y, H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('settings.register.asset-base-classification.relator.edit', $res->id) }}"
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
        {{ $response->links() }}
    </div>
</div>
