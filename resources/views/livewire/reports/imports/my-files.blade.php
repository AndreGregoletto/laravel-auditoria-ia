<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('navbar.reports') }} / {{ __('navbar.my_uploaded_files') }}
    </h2>
</x-slot>

<div class="space-y-6 py-12">
    <div class="overflow-hidden rounded-sm border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-900/40">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                    <th class="px-4 py-3">{{ __('reports.destination_service') }}</th>
                    <th class="px-4 py-3">{{ __('reports.file_name') }}</th>
                    <th class="px-4 py-3">{{ __('company.name') }}</th>
                    <th class="px-4 py-3">{{ __('reports.file_step') }}</th>
                    <th class="px-4 py-3">{{ __('reports.file_states') }}</th>
                    <th class="px-4 py-3">{{ __('reports.send_in') }}</th>
                    <th class="px-4 py-3">{{ __('reports.updated_in') }}</th>
                    <th class="px-4 py-3">{{ __('reports.actions') }}</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($files as $file)
                    <tr
                        class="
                            text-sm text-gray-800 dark:text-gray-100
                            even:bg-gray-50 dark:even:bg-gray-900/40
                            hover:bg-gray-100 dark:hover:bg-gray-800
                            focus-within:bg-indigo-50 dark:focus-within:bg-indigo-950/40
                            transition-colors
                    ">
                        <td class="px-4 py-3">{{ __("services.{$file->type_file->name}") }}</td>
                        <td class="px-4 py-3">{{ $file->file_name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center min-w-0">
                                <span class="shrink-0"></span>
                                <span class="truncate" title="{{ $file->company->name }}">
                                    {{ $file->company->name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @switch($file->file_step_id)
                                @case(1) <span class="text-yellow-500">{{ __('reports.processing') }}</span> @break
                                @case(2) <span class="text-green-500">{{ __('reports.processed') }}</span> @break
                                @case(3) <span class="text-red-500">{{ __('reports.error') }}</span> @break
                                @case(4) <span class="text-gray-500">{{ __('reports.cancelled') }}</span> @break
                                @default <span class="text-blue-400">{{ __('reports.in_queue') }}</span>
                            @endswitch
                        </td>

                        @php
                            $color = match ($file->file_status_id) {
                                1 => 'text-red-500',
                                2 => 'text-green-500',
                                3 => 'text-yellow-900',
                            };

                            $conf = match ($file->file_status_id) {
                                1 => 'inactive',
                                2 => 'active',
                                3 => 'file_generated',
                            };
                        @endphp

                        <td class="px-4 py-3 {{ $color }}">
                            {{ __("status.{$conf}") }}
                        </td>

                        <td class="px-4 py-3">{{ $file->created_at->translatedFormat('d F Y, H:i') }}</td>
                        <td class="px-4 py-3">{{ $file->updated_at->translatedFormat('d F Y, H:i') }}</td>
                        <td class="space-x-2 px-4 py-3">
                            @if($file->file_step_id === 5 && $file->file_status_id === 2)
                                <button wire:click="cancel({{ $file->id }})"
                                        class="text-red-500 hover:underline">
                                    {{ __('reports.cancel') }}
                                </button>

                            @elseif($file->file_step_id === 2 && $file->file_status_id === 3)
                                <a
                                    href="{{ route('balance.download.xlsx', ['file' => $file->id]) }}"
                                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-2 text-sm
                                        font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2
                                        focus:ring-emerald-500/40 dark:focus:ring-emerald-400/40"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3v10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 11l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                              stroke-linejoin="round"/>
                                        <path d="M5 21h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    {{ __('buttons.download') }}
                                </a>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="text-left py-6 text-gray-400 px-4">
                            {{ __('reports.no_files_uploaded_yet') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
