<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('navbar.tools') }} / {{ __('navbar.processes') }} / {{ __('navbar.validate_bal') }}
    </h2>
</x-slot>

<div class="py-6 space-y-12">
    <div class="space-y-5">

        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">

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

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('reports.file_name') }}
                    </label>
                    <input
                        type="text"
                        wire:model.live.debounce.350ms="search"
                        placeholder="{{ __('reports.search_here') ?? 'Search Here . . .' }}"
                        class="mt-1 w-full rounded-lg border-gray-300 bg-white text-sm text-gray-700
                               focus:border-indigo-500 focus:ring-indigo-500
                               dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200"
                    />
                </div>

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
                            focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-950
                            dark:text-gray-200"
                        >
                            <option value="">{{ __('labels.year') ?? 'Ano' }}</option>
                            <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                            <option value="{{ $currentYear - 1 }}">{{ $currentYear - 1 }}</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                        {{ __('navbar.only_my_files') }}
                    </label>

                    <div class="mt-1 flex h-[42px] items-center rounded-lg border border-gray-300 bg-gray-50 px-3
                        dark:border-gray-700 dark:bg-gray-900"
                    >
                        <label class="inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <input
                                type="checkbox"
                                checked
                                disabled
                                class="rounded border-gray-300 text-indigo-600 dark:border-gray-700 dark:bg-gray-900"
                            />
                            {{ __('labels.check_to_confirm') }}
                        </label>
                    </div>
                </div>


            </div>
        </div>

        {{-- Cards (Arquivos) --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                @if(!$this->filtersReady())
                    <div class="col-span-full rounded-xl border border-gray-200 bg-white p-8 text-center text-sm
                        text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400"
                    >
                        {{ __('labels.select_company_and_reference_to_list') }}
                    </div>
                @else
                    @forelse($files as $f)
                        <div
                            class="group relative overflow-hidden rounded-xl border border-gray-200 bg-white p-4
                            shadow-sm transition hover:shadow-md hover:border-indigo-300 dark:border-gray-800
                            dark:bg-gray-900 dark:hover:border-indigo-700"
                        >

                            <div class="absolute right-0 top-0 h-10 w-10 bg-gray-50 dark:bg-gray-800"
                                 style="clip-path: polygon(100% 0, 0 0, 100% 100%);"></div>

                            <div class="flex items-start gap-3">
                                @php
                                    $status = (int) ($f->file_status_id);
                                    $isValidated = $status === 3;
                                @endphp


                                <div class="shrink-0">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg
                                        {{ $isValidated
                                            ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300'
                                            : 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300 transition-transform duration-200 hover:scale-105'
                                        }}"
                                    >
                                        @if($isValidated)
                                            <a
                                                href="{{ route('balance.download.xlsx_included', ['file' => $f->id]) }}"
                                                class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700"
                                                title="{{ __('labels.download_file') }}"
                                            >
                                        @endif
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5"
                                             viewBox="0 0 24 24"
                                             fill="none">
                                            <path d="M7 3h7l4 4v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"
                                                  stroke="currentColor"
                                                  stroke-width="2"
                                                  stroke-linejoin="round"/>
                                            <path d="M14 3v5h5"
                                                  stroke="currentColor"
                                                  stroke-width="2"
                                                  stroke-linejoin="round"/>
                                            <path d="M8 12h8M8 16h8"
                                                  stroke="currentColor"
                                                  stroke-width="2"
                                                  stroke-linecap="round"/>
                                        </svg>
                                        @if($isValidated)
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $f->file_name }}
                                            </p>

                                            <div class="mt-1 space-y-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">{{ __('labels.reference_date') }}:</span> {{ sprintf('%02d/%04d', $f->reference_month, $f->reference_year) }}
                                                </p>

                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">{{ __('labels.sent_in') }}:</span> {{ optional($f->created_at)->translatedFormat('d F Y, H:i') }}
                                                </p>

                                                @if($f->file_status_id === 3)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        <span class="font-semibold">{{ __("status.file_generated") }}: </span> {{ __("labels.download_available") }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        @php
                                            $status = match($f->file_status_id){
                                                1 => __('status.inactive'),
                                                2 => __('status.active'),
                                                3 => __('labels.validated_trial_balance'),
                                            }
                                        @endphp
                                        <span class="shrink-0 inline-flex items-center rounded-full px-2 py-1
                                            text-xs font-semibold {{ $f->file_status_id === 1 ? 'text-red-500' : 'text-green-500' }}
                                            bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200"
                                        >{{ $status }}
                                        </span>

                                    </div>

                                    <div class="mt-3 h-px bg-gray-100 dark:bg-gray-800"></div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <a href="{{ route('validate-edit', $f->id) }}"
                                           target="_blank"
                                           class="text-xs font-semibold text-indigo-600 group-hover:text-indigo-700
                                                dark:text-indigo-400 dark:group-hover:text-indigo-300"
                                        >
                                            {{ __('labels.validate') }}
                                        </a>

                                        <a href="{{ route('validate.ai-preview', $f->id) }}"
                                           target="_blank"
                                           class="text-xs font-semibold text-indigo-600 group-hover:text-indigo-700
                                                dark:text-indigo-400 dark:group-hover:text-indigo-300"
                                        >
                                            {{ __('labels.automated_validate_with_ai') }} +
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400">
                            {{ __('labels.no_files_were_found_using_the_current_filters') }}
                        </div>
                    @endforelse
                @endif

            </div>
        </div>

    </div>
</div>
