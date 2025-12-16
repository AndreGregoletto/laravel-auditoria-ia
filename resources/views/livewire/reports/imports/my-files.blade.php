<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('navbar.reports') }} / {{ __('navbar.my_uploaded_files') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">

        <table class="w-full text-md">
            <thead>
            <tr class="text-left text-gray-500">
                <th>{{ __('reports.file_name') }}</th>
                <th>{{ __('reports.extension') }}</th>
                <th>{{ __('reports.file_size') }}</th>
                <th>{{ __('reports.file_states') }}</th>
                <th>{{ __('reports.status') }}</th>
                <th>{{ __('reports.send_in') }}</th>
                <th>{{ __('reports.updated_in') }}</th>
                <th>{{ __('reports.actions') }}</th>
            </tr>
            </thead>

            <tbody>
            @forelse($files as $file)
                <tr class="border-t">
                    <td>{{ $file->file_name }}</td>
                    <td>{{ $file->file_extension }}</td>
                    <td>{{ number_format($file->file_size / 1024, 1) }} KB</td>
                    <td>
                        @switch($file->file_step)
                            @case(1) <span class="text-yellow-500">{{ __('reports.processing') }}</span> @break
                            @case(2) <span class="text-green-500">{{ __('reports.processed') }}</span> @break
                            @case(3) <span class="text-red-500">{{ __('reports.error') }}</span> @break
                            @case(4) <span class="text-gray-500">{{ __('reports.cancelled') }}</span> @break
                            @default <span class="text-blue-400">{{ __('reports.in_queue') }}</span>
                        @endswitch
                    </td>

                    <td class="{{ $file->status === 1 ? 'text-green-500' : 'text-red-500' }}">
                        {{ $file->status === 1 ? __('reports.active') : __('reports.inactive') }}
                    </td>

                    <td>{{ $file->created_at->translatedFormat('d F Y, H:i') }}</td>
                    <td>{{ $file->updated_at->translatedFormat('d F Y, H:i') }}</td>
                    <td class="space-x-2">
                        @if($file->file_step === 0 && $file->status === 1)
                            <button wire:click="cancel({{ $file->id }})"
                                    class="text-red-500 hover:underline">
                                {{ __('reports.cancel') }}
                            </button>

                            <button wire:click="$emit('replaceFile', {{ $file->id }})"
                                    class="text-indigo-500 hover:underline">
                                {{ __('reports.replace') }}
                            </button>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="4" class="text-center py-6 text-gray-400">
                        {{ __('reports.no_files_uploaded_yet') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
