<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('navbar.tools') }} / {{ __('navbar.import_queue') }} / {{ __('navbar.balance') }}
    </h2>
</x-slot>

<div class="py-12">
    <div>
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @if (session()->has('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-600 text-white">
                        {{ session('success') }}
                    </div>
                @endif

                    <form wire:submit="save" class="space-y-6">

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="md:col-span-1">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('company.name') }} <span class="text-red-600">*</span>
                                </label>

                                <select
                                    wire:model.defer="form.company_id"
                                    class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                       focus:border-indigo-500 focus:ring-indigo-500
                                       dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                                >
                                    <option value="">{{ __('labels.select') }}</option>
                                    @foreach($companies as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>

                                @error('form.company_id')
                                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('reports.reference_month') }} <span class="text-red-600">*</span>
                                </label>

                                <select
                                    wire:model.defer="form.reference_month"
                                    class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                       focus:border-indigo-500 focus:ring-indigo-500
                                       dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                                >
                                    <option value="">{{ __('labels.select') }}</option>
                                    @foreach($months as $key => $m)
                                        <option value="{{ $key }}">{{ $m }}</option>
                                    @endforeach()
                                </select>

                                @error('form.reference_month')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ __('reports.reference_year') }} <span class="text-red-600">*</span>
                                </label>

                                <select
                                    wire:model.defer="form.reference_year"
                                    class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                       focus:border-indigo-500 focus:ring-indigo-500
                                       dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                                    >
                                    <option value="">{{ __('labels.select') }}</option>
                                    @foreach($years as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach()
                                </select>

                                @error('form.reference_year')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="file-upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('files.selected_file_excel') }} <span class="text-red-600">*</span>
                            </label>

                            <input
                                id="file-upload"
                                type="file"
                                wire:model="form.file"
                                class="appearance-none
                                    w-full text-sm text-gray-500 dark:text-gray-400
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-lg file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-600 file:text-white
                                    hover:file:bg-indigo-700
                                    dark:file:bg-indigo-700 dark:file:text-white
                                    cursor-pointer file:cursor-pointer"
                            />

                            @error('form.file')
                                <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror

                            @if (isset($form->file) && $form->file)
                                <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                                    {{ __('files.selected_file') }}: <strong>{{ $form->file->getClientOriginalName() }}</strong>
                                </p>
                            @endif

                            <div wire:loading wire:target="form.file" class="mt-2 text-sm text-indigo-500">
                                <span class="animate-pulse">{{ __('files.loading_file') }}</span>
                            </div>
                        </div>

                        @error('form')
                            <span class="text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</span>
                        @enderror

                        <button type="submit"
                                class="px-6 py-2 bg-gray-800 dark:bg-gray-700 text-white dark:text-gray-100 font-bold rounded-lg
                               shadow-md hover:bg-gray-900 dark:hover:bg-gray-900 transition duration-150 ease-in-out
                               focus:outline-none focus:ring-4 focus:ring-gray-500 focus:ring-opacity-50 disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="save">{{ __('buttons.send') }}</span>
                            <span wire:loading wire:target="save">{{ __('files.wait') }}</span>
                        </button>

                    </form>

            </div>
        </div>
    </div>
</div>
