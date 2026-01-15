<li
    wire:poll.10s="refreshNotifications"
    class="pc-h-item"
    x-data="{
        open:false,
        top: 0,
        left: 0,
        place() {
            if (!this.$refs.btn) return;
            const r = this.$refs.btn.getBoundingClientRect();
            this.top  = r.bottom + 10;
            this.left = Math.min(window.innerWidth - 420, r.right - 400);
        }
    }"
    @keydown.escape.window="open=false"
>
    <a href="#"
       x-ref="btn"
       class="pc-head-link relative"
       @click.prevent="open = !open; if(open){ $nextTick(()=>place()) }"
    >
        <i class="ph ph-bell"></i>

        @if($unreadCount > 0)
            <span class="badge bg-success-500 text-white rounded-full z-10 absolute right-0 top-0">
                {{ $unreadCount }}
            </span>
        @endif
    </a>

    <template x-teleport="body">
        <div x-show="open" style="display:none;">
            <div class="fixed inset-0 z-[9998] bg-black/20" @click="open=false"></div>

            <div
                class="fixed z-[9999] w-[400px] max-w-[92vw] rounded-xl overflow-hidden
                       bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-800"
                :style="`top:${top}px; left:${left}px;`"
                @resize.window="place()"
                @scroll.window="place()"
            >
                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ __('navbar.notifications') }}
                    </h3>

                    <button
                        type="button"
                        wire:click="clearAll"
                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800
                               dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        {{ __('navbar.clear_all_notifications') }}
                    </button>
                </div>

                <div class="p-3 max-h-[60vh] overflow-y-auto space-y-2">
                    @forelse($items as $n)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $n['read'] ? __('notifications.read') : __('notifications.new') }}
                                    </p>

                                    <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                        {{ __("notifications.{$n['message']}") ?? $n['message'] }}
                                    </p>

                                    @if(!empty($n['file_id']))
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                            {{ __('reports.file_name') }} : <span class="text-black/50 hover:text-black/70"> {{ $n['file_name'] }}</span>
                                        </p>
                                    @endif

                                    <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                        {{ $n['created_at'] }}
                                    </p>
                                </div>

                                <div class="flex flex-col items-end gap-2 shrink-0">
                                    @if(!$n['read'])
                                        <button
                                            type="button"
                                            wire:click="markAsRead({{ $n['id'] }})"
                                            class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-xs font-semibold
                                                   text-indigo-600 hover:text-indigo-800
                                                   dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            <i class="ph ph-check-circle"></i>
                                            {{ __('buttons.mark_as_read') }}
                                        </button>
                                    @endif

                                    @if(!empty($n['file_id']))

                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold
                                            bg-gray-100 hover:bg-gray-200 text-gray-800
                                            dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100"
                                        >
                                            <i class="ph ph-download-simple"></i>
                                            {{ __('buttons.download') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ __('notifications.no_notifications') }}
                        </div>
                    @endforelse
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 text-center">
                    <button
                        type="button"
                        class="text-xs text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        @click="open=false"
                    >
                        {{ __('buttons.close') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</li>
