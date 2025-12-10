<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Fila de Importação / Balancete') }}
    </h2>
</x-slot>

<div class="py-12">
    <div>
{{--    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">--}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                @if (session()->has('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-600 text-white">
                        {{ session('success') }}
                    </div>
                @endif

                <form wire:submit="save" class="space-y-6">

                    <div class="mb-6">
                        <label for="file-upload" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Selecione o Arquivo (Excel)
                        </label>

                        <input
                            id="file-upload"
                            type="file"
                            wire:model="form.file"
                            class="
                                block w-full text-sm text-gray-500 dark:text-gray-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-600 file:text-white dark:file:bg-indigo-700
                                hover:file:bg-indigo-700 dark:hover:file:bg-indigo-600
                                file:cursor-pointer
                                border border-gray-300 dark:border-gray-700 rounded-lg
                            "
                        />

                        @if ($form->file)
                            <p class="mt-2 text-sm text-green-600 dark:text-green-400">
                                Arquivo selecionado: **{{ $form->file->getClientOriginalName() }}**
                            </p>
                        @endif

                        <div wire:loading wire:target="form.file" class="mt-2 text-sm text-indigo-500">
                            <span class="animate-pulse">Carregando arquivo (aguarde)...</span>
                        </div>
                    </div>

                    <div>
                        @error('form.file')
                            <span class="text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit"
                            class="
                                px-6 py-2
                                bg-gray-800 dark:bg-gray-700
                                text-white dark:text-gray-100
                                font-bold
                                rounded-lg
                                shadow-md
                                hover:bg-gray-900 dark:hover:bg-gray-900
                                transition duration-150 ease-in-out
                                focus:outline-none focus:ring-4 focus:ring-gray-500 focus:ring-opacity-50
                                disabled:opacity-50
                            "
                            wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="save">Enviar</span>
                        <span wire:loading wire:target="save">Aguarde...</span>
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>
