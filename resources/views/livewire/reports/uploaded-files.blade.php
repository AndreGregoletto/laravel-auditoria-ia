<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('navbar.reports') }} / {{ __('navbar.files') }} / {{ __('navbar.sent') }}
    </h2>
</x-slot>

<div class="space-y-6 py-12">
    <div class="space-y-4">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <input type="text"
                   wire:model.live.debounce.400ms="filterFileName"
                   placeholder="{{ __('reports.file_name') }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                      focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />

            <input type="text"
                   wire:model.live.debounce.400ms="filterUser"
                   placeholder="{{ __('reports.user') }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                      focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />

            <input type="text"
                   wire:model.live.debounce.400ms="filterCompany"
                   placeholder="{{ __('reports.company') }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                      focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />

            <select wire:model.live="filterService"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.destination_service') }}</option>
                @foreach($typeFile as $file)
                    <option value="{{ $file->id }}">{{ __("services.{$file->name}") }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStep"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.file_step') }}</option>
                @foreach($fileStep as $file)
                    <option value="{{ $file->id }}">{{ __("reports.{$file->name_conf}") }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.file_states') }}</option>
                @foreach($fileStatus as $file)
                    <option value="{{ $file->id }}">{{ __("reports.{$file->name_conf}") }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <select wire:model.live="filterMonth"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.reference_month') }}</option>
                @foreach($months as $key => $m)
                    <option value="{{ $key }}">{{ $m }}</option>
                @endforeach()
            </select>

            <select wire:model.live="filterYear"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100">
                <option value="">{{ __('reports.reference_year') }}</option>
                @foreach($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach()
            </select>

            <input type="text"
                   wire:model.live.debounce.400ms="filterExtension"
                   placeholder="{{ __('reports.extension') }}"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900
                      focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
            />

            <div class="sm:col-span-2 lg:col-span-3 flex gap-2">
                <button type="button"
                        wire:click="clearFilters"
                        class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-700 hover:bg-gray-50
                           dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                    {{ __('reports.clear_filters') }}
                </button>

                <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                    {{ $files->total() }} {{ __('reports.records') }}
                </div>
            </div>
        </div>
    </div>


    <div class="relative overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
        <table class="min-w-[1400px] w-full table-auto divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                    <th class="px-4 py-3 w-[120px]">{{ __('reports.destination_service') }}</th>
                    <th class="px-4 py-3 w-[120px]">{{ __('reports.file_name') }}</th>
                    <th class="px-4 py-3 w-[120px]">{{ __('reports.user') }}</th>
                    <th class="px-4 py-3 w-[120px]">{{ __('reports.company') }}</th>
                    <th class="px-4 py-3 w-[80px] text-center">{{ __('reports.reference_month') }}</th>
                    <th class="px-4 py-3 w-[80px] text-center">{{ __('reports.reference_year') }}</th>
                    <th class="px-4 py-3 w-[80px] text-center">{{ __('reports.extension') }}</th>
                    <th class="px-4 py-3 w-[100px] text-right">{{ __('reports.file_size') }}</th>
                    <th class="px-4 py-3 w-[110px] text-center">{{ __('reports.file_step') }}</th>
                    <th class="px-4 py-3 w-[100px] text-center">{{ __('reports.file_states') }}</th>
                    <th class="px-4 py-3 w-[160px]">{{ __('reports.send_in') }}</th>
                    <th class="px-4 py-3 w-[160px]">{{ __('reports.updated_in') }}</th>
                    <th class="px-4 py-3 w-[120px] text-center">{{ __('reports.actions') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-sm">
            @forelse($files as $file)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-4 py-3 font-medium">
                        {{ __("services.{$file->type_file->name}") }}
                    </td>

                    <td class="px-4 py-3 max-w-[220px] truncate" title="{{ $file->file_name }}">
                        {{ $file->file_name }}
                    </td>

                    <td class="px-4 py-3 max-w-[160px] truncate" title="{{ $file->user->name }}">
                        {{ $file->user->name }}
                    </td>

                    <td class="px-4 py-3 max-w-[220px] truncate" title="{{ $file->company->name }}">
                        {{ $file->company->name }}
                    </td>

                    <td class="px-4 py-3 text-center">{{ __("labels.{$file->reference_month}") }}</td>
                    <td class="px-4 py-3 text-center">{{ $file->reference_year }}</td>

                    <td class="px-4 py-3 text-center uppercase">
                        {{ $file->file_extension }}
                    </td>

                    <td class="px-4 py-3 text-right">
                        {{ number_format($file->file_size / 1024, 1) }} KB
                    </td>

                    <td class="px-4 py-3 text-center">
                        @switch($file->file_step_id)
                            @case(1) <span class="text-yellow-500">{{ __('reports.processing') }}</span> @break
                            @case(2) <span class="text-green-500">{{ __('reports.processed') }}</span> @break
                            @case(3) <span class="text-red-500">{{ __('reports.error') }}</span> @break
                            @case(4) <span class="text-gray-500">{{ __('reports.cancelled') }}</span> @break
                            @default <span class="text-blue-400">{{ __('reports.in_queue') }}</span>
                        @endswitch
                    </td>

                    <td class="px-4 py-3 text-center {{ $file->file_status_id === 1 ? 'text-red-500' : 'text-green-500' }}">
                        {{ $file->file_status_id === 1 ? __('reports.inactive') : __('reports.active') }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $file->created_at->translatedFormat('d M Y, H:i') }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $file->updated_at->translatedFormat('d M Y, H:i') }}
                    </td>

                    <td class="px-4 py-3 text-center">
                        <button wire:click="download({{ $file->id }})"
                                class="text-indigo-600 hover:underline">
                            {{ __('buttons.download') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="py-6 text-center text-gray-400">
                        {{ __('reports.no_files_uploaded_yet') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $files->links() }}
    </div>
</div>
