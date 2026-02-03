<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ __('navbar.my_profile') }}
                / {{ __('navbar.my_notification') }}
            </h1>
        </div>
    </x-slot>

    <div class="space-y-6 py-12">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-900/40">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                        <th class="px-4 py-3 ">{{ __('notifications.message') }}</th>
                        <th class="px-4 py-3 ">{{ __('reports.file_name') }}</th>
                        <th class="px-4 py-3 ">{{ __('reports.destination_service') }}</th>
                        <th class="px-4 py-3 ">{{ __('reports.company') }}</th>
                        <th class="px-4 py-3 ">{{ __('notifications.read') }}</th>
                        <th class="px-4 py-3 ">{{ __('reports.created_in') }}</th>
                        <th class="px-4 py-3 ">{{ __('reports.updated_in') }}</th>
                        <th class="px-4 py-3 ">{{ __('reports.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($msg as $m)
                            <tr
                                class="
                                    text-sm text-gray-800 dark:text-gray-100
                                    even:bg-gray-50 dark:even:bg-gray-900/40
                                    hover:bg-gray-100 dark:hover:bg-gray-800
                                    focus-within:bg-indigo-50 dark:focus-within:bg-indigo-950/40
                                    transition-colors
                                "
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center min-w-0">
                                        <span class="shrink-0"></span>
                                        @php
                                            $default =  __("notifications.{$m['message']}") ?? $m['message'];
                                            $default = $default ?? $m['msg_system'];
                                        @endphp
                                        <span class="truncate" title="{{ $default }}">
                                            {{ $default }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center min-w-0">
                                        <span class="shrink-0"></span>
                                        <span class="truncate" title="{{ $m['file_name'] }}">
                                            {{ $m['file_name'] }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3" class="truncate">
                                    {{ $m['type_file'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center min-w-0">
                                        <span class="shrink-0"></span>
                                        <span class="truncate" title="{{ $m['company'] }}">
                                            {{ $m['company'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold {{
                                            $m['read']
                                                ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-200'
                                                : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200'
                                    }}">
                                        {{ $m['read'] ? __('notifications.read') : __('notifications.unread') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $m['created_at']->translatedFormat('d F Y, H:i') }}</td>
                                <td class="px-4 py-3">{{ $m['updated_at']->translatedFormat('d F Y, H:i') }}</td>
                                <td class="px-4 py-3">{{ __('reports.download') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('reports.no_results_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
