<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('navbar.tools') }} / {{ __('navbar.processes') }} / {{ __('navbar.generate_rag') }}
    </h2>
</x-slot>

@php
    $currentYear = now()->year;

    $months = [
        1 => __('labels.january')   ?? 'Janeiro',
        2 => __('labels.february')  ?? 'Fevereiro',
        3 => __('labels.march')     ?? 'Março',
        4 => __('labels.april')     ?? 'Abril',
        5 => __('labels.may')       ?? 'Maio',
        6 => __('labels.june')      ?? 'Junho',
        7 => __('labels.july')      ?? 'Julho',
        8 => __('labels.august')    ?? 'Agosto',
        9 => __('labels.september') ?? 'Setembro',
        10 => __('labels.october')  ?? 'Outubro',
        11 => __('labels.november') ?? 'Novembro',
        12 => __('labels.december') ?? 'Dezembro',
    ];
@endphp

<div class="py-6 space-y-12">
    <div class="space-y-5">

        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('reports.company') }}<span class="text-red-500">*</span>
                    </label>

                    <select
                        wire:model.live="companyId"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                               focus:border-indigo-500 focus:ring-indigo-500
                               dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                    >
                        <option value="">{{ __('labels.select') }}</option>
                         @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->commercial_name ?? $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Only my files (alinhado como campo) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('navbar.only_my_files') }}
                    </label>

                    <div class="mt-1 flex h-[42px] items-center rounded-lg border border-gray-300 bg-white px-3
                                dark:border-gray-700 dark:bg-gray-950">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                            <input
                                type="checkbox"
                                wire:model.live="onlyMyFiles"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500
                                       dark:border-gray-700 dark:bg-gray-950"
                            />
                            {{ __('labels.check_to_confirm') }}
                        </label>
                    </div>
                </div>

                {{-- Referência Início (Mês/Ano) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('labels.reference_start') }}<span class="text-red-500">*</span>
                    </label>

                    <div class="mt-1 grid grid-cols-2 gap-2">
                        <select
                            wire:model.live="refMonthFrom"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                        >
                            <option value="">{{ __('labels.month') ?? 'Mês' }}</option>
                            @foreach($months as $m => $label)
                                <option value="{{ $m }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <select
                            wire:model.live="refYearFrom"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                        >
                            <option value="">{{ __('labels.year') ?? 'Ano' }}</option>
                            <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                            <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                        </select>
                    </div>
                </div>

                {{-- Referência Fim (Mês/Ano) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('labels.reference_end') }}<span class="text-red-500">*</span>
                    </label>

                    <div class="mt-1 grid grid-cols-2 gap-2">
                        <select
                            wire:model.live="refMonthTo"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                        >
                            <option value="">{{ __('labels.month') ?? 'Mês' }}</option>
                            @foreach($months as $m => $label)
                                <option value="{{ $m }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <select
                            wire:model.live="refYearTo"
                            class="w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                                   focus:border-indigo-500 focus:ring-indigo-500
                                   dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                        >
                            <option value="">{{ __('labels.year') ?? 'Ano' }}</option>
                            <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                            <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        {{-- Card Dual List --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-12">

                {{-- Disponíveis --}}
                <div class="lg:col-span-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('labels.available_files') ?? 'Arquivos disponíveis' }}
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                             {{ $this->availableFiles->count() }}
                        </span>
                    </div>

                    <select
                        multiple
                        size="14"
                        wire:model="availableSelectedIds"
                        class="mt-2 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                           focus:border-indigo-500 focus:ring-indigo-500
                           dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                    >
                        @if(!$this->filtersReady())
                            <option disabled>
                                {{ __('labels.select_company_and_reference_to_list') }}
                            </option>
                        @else
                            @forelse($this->availableFiles as $f)
                                <option value="{{ $f->id }}">
                                    {{ sprintf('%02d/%04d', $f->reference_month, $f->reference_year) }}
                                    — {{ $f->file_name }}
                                </option>
                            @empty
                                <option disabled>{{__('labels.no_files_were_found_using_the_current_filters')}}</option>
                            @endforelse
                        @endif
                    </select>
                </div>

                <div class="lg:col-span-2 flex flex-col justify-center gap-2">
                    <button
                        type="button"
                        wire:click="addSelected"
                        class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white
                               hover:bg-indigo-700 disabled:opacity-50"
                    >
                        {{ __('labels.add_file') ?? 'Adicionar' }} →
                    </button>

                    <button
                        type="button"
                        wire:click="clearSelected"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700
                               hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                    >
                        {{ __('labels.clear') ?? 'Limpar' }}
                    </button>
                </div>

                <div class="lg:col-span-5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ __('labels.selected_files') ?? 'Selecionados' }}
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $this->selectedFiles->count() }} {{ __('labels.items') ?? 'itens' }}
                        </span>
                    </div>

                    <div class="mt-2 rounded-lg border border-gray-300 dark:border-gray-700">
                        <div class="max-h-[360px] overflow-y-auto">

                            @forelse($this->selectedFiles as $f)
                                <div class="flex items-start justify-between gap-3 border-b border-gray-200 p-2 dark:border-gray-800">
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-medium text-gray-800 dark:text-gray-100">
                                            {{ sprintf('%02d/%04d', $f->reference_month, $f->reference_year) }}
                                            — {{ $f->file_name }}
                                        </div>

                                        @if(!empty($f->error_log))
                                            <div class="mt-1 text-xs text-rose-600 dark:text-rose-400 line-clamp-2">
                                                {{ $f->error_log }}
                                            </div>
                                        @endif
                                    </div>

                                    <button
                                        type="button"
                                        wire:click="removeSelected({{ $f->id }})"
                                        class="shrink-0 rounded-lg px-2 py-1 text-xs font-medium
                                            bg-rose-600 text-white hover:bg-rose-700"
                                        title="{{ __('labels.remove') }}"
                                    >
                                        {{ __('labels.remove') }}
                                    </button>
                                </div>
                            @empty
                                <div class="p-3 text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('labels.no_selected_files') }}
                                </div>
                            @endforelse

                        </div>
                    </div>
                    @php
                        $canGenerate = count($this->selectedFileIds) >= 2;
                    @endphp
                    <button
                        type="button"
                        wire:click="generateRag"
                        wire:loading.attr="disabled"
                        @disabled(empty($this->selectedFileIds))
                        class="mt-3 w-full rounded-lg px-3 py-2 text-sm font-medium
                           {{ !$canGenerate
                                ? 'bg-gray-300 text-gray-700 cursor-not-allowed'
                                : 'bg-indigo-600 text-white hover:bg-indigo-700'
                           }}"
                    >
                        <span wire:loading.remove>{{ __('navbar.generate_rag') }}</span>
                        <span wire:loading>{{ __('files.wait') }}</span>
                    </button>
                </div>


            </div>
        </div>

    </div>
</div>
