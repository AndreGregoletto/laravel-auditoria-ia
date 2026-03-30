<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }}
            / {{ __('navbar.register') }}
            / {{ __('reports.bp_classification') }}
            / {{ __('navbar.copy') }}
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

        <form wire:submit.prevent="openConfirm" class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('reports.copy_source') }}
                    </h2>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('reports.source_configuration') }}
                        </label>

                        <select
                            wire:model="form.source_key"
                            class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        >
                            @foreach($sourceOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        @error('form.source_key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                    <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('reports.copy_target') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('reports.tree') }}
                            </label>

                            <select
                                wire:model="form.target_tree_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option value="">{{ __('labels.select') }}</option>
                                @foreach($companyTrees as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>

                            @error('form.target_tree_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('reports.company') }}
                            </label>

                            <select
                                wire:model="form.target_company_id"
                                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option value="">{{ __('labels.select') }}</option>
                                @foreach($companies as $id => $label)
                                    <option value="{{ $id }}">{{ $label }}</option>
                                @endforeach
                            </select>

                            @error('form.target_company_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-amber-200">
                {{ __('reports.copy_configuration_warning') }}
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    {{ __('buttons.copy') }}
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

    @if($showConfirmModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50" wire:click="closeConfirm"></div>

            <div class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-lg dark:bg-gray-900">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {{ __('labels.confirm_action') }}
                </h2>

                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('reports.confirm_copy_configuration') }}
                </p>

                <p class="mt-3 text-sm text-amber-700 dark:text-amber-300">
                    {{ __('reports.copy_configuration_warning') }}
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        wire:click="closeConfirm"
                        class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800"
                    >
                        {{ __('buttons.cancel') }}
                    </button>

                    <button
                        type="button"
                        wire:click="save"
                        class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        {{ __('buttons.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>