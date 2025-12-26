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
{{--                    <th class=px-4 py-3>{{ __('reports.extension') }}</th>--}}
{{--                    <th class=px-4 py-3>{{ __('reports.file_size') }}</th>--}}
                    <th class="px-4 py-3">{{ __('reports.file_states') }}</th>
                    <th class="px-4 py-3">{{ __('reports.status') }}</th>
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
{{--                        <td class="px-4 py-3">{{ $file->file_extension }}</td>--}}
{{--                        <td class="px-4 py-3">{{ number_format($file->file_size / 1024, 1) }} KB</td>--}}
                        <td class="px-4 py-3">
                            @switch($file->file_step_id)
                                @case(1) <span class="text-yellow-500">{{ __('reports.processing') }}</span> @break
                                @case(2) <span class="text-green-500">{{ __('reports.processed') }}</span> @break
                                @case(3) <span class="text-red-500">{{ __('reports.error') }}</span> @break
                                @case(4) <span class="text-gray-500">{{ __('reports.cancelled') }}</span> @break
                                @default <span class="text-blue-400">{{ __('reports.in_queue') }}</span>
                            @endswitch
                        </td>
                        <td class="px-4 py-3 {{ $file->file_status_id === 1 ? 'text-green-500' : 'text-red-500' }}">
                            {{ $file->file_status_id === 1 ? __('reports.active') : __('reports.inactive') }}
                        </td>

                        <td class="px-4 py-3">{{ $file->created_at->translatedFormat('d F Y, H:i') }}</td>
                        <td class="px-4 py-3">{{ $file->updated_at->translatedFormat('d F Y, H:i') }}</td>
                        <td class="space-x-2 px-4 py-3">
                            @if($file->file_step_id === 5 && $file->file_status_id === 1)
                                <button wire:click="cancel({{ $file->id }})"
                                        class="text-red-500 hover:underline">
                                    {{ __('reports.cancel') }}
                                </button>

                                <button wire:click="$emit('replaceFile', {{ $file->id }})"
                                        class="text-indigo-500 hover:underline">
                                    {{ __('reports.replace') }}
                                </button>
                            @elseif($file->file_step_id === 6 && $file->file_status_id === 1)
                                <button wire:click="$emit('replaceFile', {{ $file->id }})"
                                        class="text-yellow-500 hover:underline">
                                    {{ __('reports.download') }}
                                </button>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="text-left py-6 text-gray-400">
                            {{ __('reports.no_files_uploaded_yet') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
