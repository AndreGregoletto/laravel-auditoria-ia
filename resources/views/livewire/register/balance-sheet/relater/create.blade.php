<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }}
            / {{ __('navbar.register') }}
            / {{ __('reports.bp_classification') }}
            / {{ __('buttons.create') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
        @if (session()->has('success'))
            <div class="mb-4 rounded-lg bg-emerald-100 px-4 py-3 text-sm text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('reports.tree') }}
                    </label>

                    <select
                        wire:model.live="form.company_tree_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    >
                        <option value="">{{ __('labels.select') }}</option>
                        @foreach($companyTrees as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    @error('form.company_tree_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('reports.company') }}
                    </label>

                    <select
                        wire:model.live="form.company_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    >
                        <option value="">{{ __('labels.select') }}</option>
                        @foreach($companies as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    @error('form.company_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('reports.classification') }}
                    </label>

                    <select
                        wire:model="form.balance_sheet_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    >
                        <option value="">{{ __('labels.select') }}</option>

                        @foreach($group as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    @error('form.balance_sheet_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-3">
                    <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('reports.value') }}
                    </label>

                    <input
                        type="text"
                        wire:model="form.value"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                    />

                    @error('form.value')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2 pt-7">
                    <input
                        id="status"
                        type="checkbox"
                        wire:model="form.status"
                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />

                    <label for="status" class="text-sm text-gray-700 dark:text-gray-300">
                        {{ __('reports.active') }}
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    {{ __('buttons.save_change') }}
                </button>

                <a
                    href="{{ route('settings.register.asset-base-classification.relator.index') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    {{ __('buttons.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>