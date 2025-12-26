<x-slot name="header">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('navbar.company_tree') }} / {{ __('reports.org_chart') }} / {{ $tree['company']->commercial_name ?? $tree['company']->name }}
        </h1>
    </div>
</x-slot>

<div class="py-12">
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div
            x-data="orgChartPanZoom()"
            class="relative"
        >
            <div class="sticky top-0 z-10 mb-3 flex items-center gap-2 bg-white/80 p-2 backdrop-blur dark:bg-gray-900/80 rounded-lg border border-gray-200 dark:border-gray-800">
                <button type="button"
                        class="px-3 py-1.5 rounded-md border text-sm dark:border-gray-700"
                        @click="zoomOut()">
                    −
                </button>

                <button type="button"
                        class="px-3 py-1.5 rounded-md border text-sm dark:border-gray-700"
                        @click="zoomIn()">
                    +
                </button>

                <button type="button"
                        class="px-3 py-1.5 rounded-md border text-sm dark:border-gray-700"
                        @click="reset()">
                    {{ __('settings.reset') }}
                </button>

                <div class="ml-2 text-xs text-gray-600 dark:text-gray-300">
                    Zoom: <span x-text="Math.round(scale * 100) + '%'"></span>
                    <span class="ml-2 text-gray-400">{{ __('settings.drag_to_move') }}</span>
                </div>
            </div>

            <div class="max-h-[calc(100vh-220px)] overflow-auto rounded-lg border border-gray-200 dark:border-gray-800">
                <div class="min-w-max w-max p-6">
                    <div
                        class="w-max"
                        :style="transformStyle"
                        @mousedown="startDrag($event)"
                        @mousemove.window="onDrag($event)"
                        @mouseup.window="endDrag()"
                        @mouseleave.window="endDrag()"
                    >
                        @if(!empty($tree))
                            @include('livewire.company-tree.organizational-chart.partials.node', ['node' => $tree])
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __('reports.no_results_found') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('orgChartPanZoom', () => ({
                    scale: 1,
                    minScale: 0.6,
                    maxScale: 1.4,
                    step: 0.1,

                    x: 0,
                    y: 0,

                    dragging: false,
                    startX: 0,
                    startY: 0,
                    baseX: 0,
                    baseY: 0,

                    get transformStyle() {
                        return `transform: translate(${this.x}px, ${this.y}px) scale(${this.scale}); transform-origin: top left;`;
                    },

                    zoomIn() {
                        this.scale = Math.min(this.maxScale, +(this.scale + this.step).toFixed(2));
                    },
                    zoomOut() {
                        this.scale = Math.max(this.minScale, +(this.scale - this.step).toFixed(2));
                    },
                    reset() {
                        this.scale = 1;
                        this.x = 0;
                        this.y = 0;
                    },

                    startDrag(e) {
                        const interactive = e.target.closest('button, a, input, select, textarea');
                        if (interactive) return;

                        this.dragging = true;
                        this.startX = e.clientX;
                        this.startY = e.clientY;
                        this.baseX = this.x;
                        this.baseY = this.y;
                    },
                    onDrag(e) {
                        if (!this.dragging) return;
                        this.x = this.baseX + (e.clientX - this.startX);
                        this.y = this.baseY + (e.clientY - this.startY);
                    },
                    endDrag() {
                        this.dragging = false;
                    },
                }));
            });
        </script>

    </div>
</div>
