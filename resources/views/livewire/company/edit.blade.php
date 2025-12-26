<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }} / {{ __('navbar.company') }} / {{ __('navbar.edit') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">
    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.name') }}</label>
                <input type="text" wire:model="form.name"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                @error('form.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.commercial_name') }}</label>
                <input type="text" wire:model="form.commercial_name"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                @error('form.commercial_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('reports.cnpj') }}</label>
                <input type="text" wire:model="form.cnpj"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" />
                @error('form.cnpj') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

        </div>

        <div class="flex flex-wrap items-center gap-6">

            <div class="flex items-center gap-2">
                <input id="publicity_trade" type="checkbox" wire:model="form.publicity_trade"
                       class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <label for="publicity_trade" class="text-sm text-gray-700 dark:text-gray-300">{{ __('reports.publicity_trade') }}</label>
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
                {{ __('buttons.save_change') }}
            </button>

            <a href="{{ route('settings.companies.index') }}"
               class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                {{ __('buttons.cancel') }}
            </a>
        </div>
    </form>
</div>
