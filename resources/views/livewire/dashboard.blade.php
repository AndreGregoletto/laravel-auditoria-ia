<div class="py-6 space-y-5">
    {{-- TOP (50% da tela mais ou menos) --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('labels.archives_today') }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.summary_imports_generation') }}</p>
            </div>

            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ now()->translatedFormat('d F Y, H:i') }}
            </div>
        </div>

        <div class="mt-4">
            <div wire:ignore class="relative h-[280px]">
                <canvas id="importsTodayChart"></canvas>
            </div>
        </div>


        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-6">
            <x-dashboard.kpi label="{{ __('labels.total') }}" :value="$today['all_file_import']" type="info" />
            <x-dashboard.kpi label="{{ __('labels.success') }}" :value="$today['file_import_success']" type="success" />
            <x-dashboard.kpi label="{{ __('labels.error') }}" :value="$today['file_import_error']" type="error" />
            <x-dashboard.kpi label="{{ __('labels.balance') }}" :value="$today['file_import_balance']" type="info" />
            <x-dashboard.kpi label="{{ __('labels.balance_generated') }}" :value="$today['file_import_balance_generate']" type="success" />
            <x-dashboard.kpi label="{{ __('labels.generated_total') }}" :value="$today['all_file_import_generate']" type="success" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">TBD}</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Sugestões abaixo</span>
            </div>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    <li>Últimos arquivos importados (com status)</li>
                    <li>Fila de processamento (jobs)</li>
                    <li>Top divergências / alertas (RAG)</li>
                    <li>Notificações do usuário (HeaderNotifications)</li>
                </ul>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('labels.registrations_today') }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.registrations_of_the_day') }}</p>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.registered_companies') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $today['companies_created'] ?? 0 }}
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('labels.trees_created_top') }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $today['tree_companies_created'] ?? 0 }}
                    </div>
                </div>
            </div>

            {{-- espaço pra você adicionar mais cards depois --}}
            <div class="mt-4 rounded-lg border border-dashed border-gray-300 p-4 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
                Próximas métricas aqui (ex: usuários ativos, validações AI, notificações, etc.)
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function renderDashboardChart() {
            const el = document.getElementById('importsTodayChart');
            if (!el) return;

            if (el._chart) el._chart.destroy();

            const data = @js($today);

            el._chart = new Chart(el, {
                type: 'bar',
                data: {
                    labels: [
                        "{{ __('labels.total') }}",
                        "{{ __('labels.success') }}",
                        "{{ __('labels.error') }}",
                        "{{ __('labels.balance') }}",
                        "{{ __('labels.balance_generated') }}",
                        "{{ __('labels.generated_total') }}"
                    ],
                    datasets: [{
                        data: [
                            data.all_file_import ?? 0,
                            data.file_import_success ?? 0,
                            data.file_import_error ?? 0,
                            data.file_import_balance ?? 0,
                            data.file_import_balance_generate ?? 0,
                            data.all_file_import_generate ?? 0,
                        ],
                        borderWidth: 2,
                        borderRadius: 6,
                        backgroundColor: ['#3b82f6','#22c55e','#ef4444','#3b82f6','#22c55e','#22c55e'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { ticks: { maxRotation: 0, minRotation: 0 } },
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderDashboardChart();

            // Livewire 3: garante render após updates
            document.addEventListener('livewire:init', () => {
                if (window.Livewire) {
                    Livewire.hook('message.processed', () => {
                        renderDashboardChart();
                    });
                }
            });
        });
    </script>
@endpush

