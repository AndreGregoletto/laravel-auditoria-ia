<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.settings') }}
            / {{ __('navbar.company_tree') }}
            / {{ __('navbar.new') }}
        </h1>
    </div>
</x-slot>

<div class="space-y-6 py-12">

    <form wire:submit="save" class="space-y-6">

        {{-- SELECT: COMPANY PARENT --}}
        <div class="max-w-md">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ __('reports.company_parent') }} <span class="text-red-600">*</span>
            </label>

            <select wire:model.live="form.company_tree_id"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm
                           focus:border-indigo-500 focus:ring-indigo-500
                           dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">
                    {{ __('reports.select_company') }}
                </option>

                @foreach($companies as $company)
                    <option value="{{ $company->id }}">
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>

            @error('form.company_tree_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- CHECKBOX STATUS --}}
        <div class="flex items-center gap-2">
            <input id="status"
                   type="checkbox"
                   wire:model.live="form.status"
                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />

            <label for="status" class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('reports.active') }}
            </label>

            @error('form.status')
            <p class="ml-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2
                           text-sm font-semibold text-white hover:bg-indigo-700">
                {{ __('buttons.save') }}
            </button>

            <a href="{{ route('companies.index') }}"
               class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2
                      text-sm font-semibold text-gray-700 hover:bg-gray-50
                      dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                {{ __('buttons.cancel') }}
            </a>
        </div>

    </form>
</div>
