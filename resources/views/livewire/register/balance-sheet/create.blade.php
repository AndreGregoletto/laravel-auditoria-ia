<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }} / {{ __('navbar.register') }} / {{ __('reports.bp_classification') }} / {{ __('navbar.new') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">
    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-3 grid-cols-1 md:grid-cols-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.code') }}</label>
                <input type="text" wire:model="form.code"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                @error('form.code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.name') }}</label>
                <input type="text" wire:model="form.name"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.tree') }}</label>
                <select wire:model="form.company_tree_id" disabled
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">{{ __('reports.tree') }}</option>
                </select>
                @error('form.company_tree_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.company') }}</label>
                <select wire:model="form.company_id" disabled
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">{{ __('labels.select') }}</option>
                </select>
                @error('form.company_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.parent_code') }}</label>
                <select wire:model="form.parent_code"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">{{ __('labels.select') }}</option>
                    @foreach(($group ?? []) as $k => $g)
                        <option value="{{ $k }}">{{ $g }}</option>
                    @endforeach
                </select>
                @error('form.parent_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.side') }}</label>
                <select wire:model="form.side"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">{{ __('labels.select') }}</option>
                    <option value="assets">{{ __('classification.assets') }}</option>
                    <option value="liabilities">{{ __('classification.liabilities') }}</option>
                    <option value="equity">{{ __('classification.equity') }}</option>
                </select>
                @error('form.side') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.section') }}</label>
                <select wire:model="form.section"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                    <option value="">{{ __('labels.select') }}</option>
                    <option value="current">{{ __('classification.current') }}</option>
                    <option value="non_current">{{ __('classification.non_current') }}</option>
                    <option value="equity">{{ __('classification.equity') }}</option>
                </select>
                @error('form.section') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.sort_order') }}</label>
                <input type="number" wire:model="form.sort_order"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                @error('form.sort_order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2">
                <input id="status" type="checkbox" wire:model="form.status"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <label for="status" class="text-sm text-gray-700 dark:text-gray-300">{{ __('reports.active') }}</label>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                {{ __('buttons.save') }}
            </button>

            <a href="{{ route('settings.register.asset-base-classification.index') }}"
               class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                {{ __('buttons.cancel') }}
            </a>
        </div>
    </form>
</div>
