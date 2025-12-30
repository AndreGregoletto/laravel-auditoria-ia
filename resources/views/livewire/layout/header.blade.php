<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
};
?>

<header class="pc-header">
    <div class="header-wrapper flex max-sm:px-[15px] px-[25px] grow"><!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse max-lg:hidden lg:inline-flex">
                    <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="sidebar-hide">
                        <i class="ph ph-list"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup lg:hidden">
                    <a href="#" class="pc-head-link ltr:!ml-0 rtl:!mr-0" id="mobile-collapse">
                        <i class="ph ph-list"></i>
                    </a>
                </li>
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <i class="ph ph-magnifying-glass"></i>
                    </a>
                    <div class="dropdown-menu pc-h-dropdown drp-search">
                        <form class="px-2 py-1">
                            <input type="search" class="form-control !border-0 !shadow-none" placeholder="{{ __('navbar.search_here') }}" />
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
                <li class="pc-h-item"
                    x-data="{
                        open:false,
                        top: 0,
                        left: 0,
                        place() {
                            if (!this.$refs.btn) return;
                            const r = this.$refs.btn.getBoundingClientRect();
                            this.top  = r.bottom + 10;
                            // modal um pouco maior que a do perfil
                            this.left = Math.min(window.innerWidth - 420, r.right - 400);
                        }
                    }"
                    @keydown.escape.window="open=false"
                >
                    <!-- Botão (sino) -->
                    <a href="#"
                       x-ref="btn"
                       class="pc-head-link relative"
                       @click.prevent="open = !open; if(open){ $nextTick(()=>place()) }">

                        <i class="ph ph-bell"></i>

                        <!-- badge -->
                        <span class="badge bg-success-500 text-white rounded-full z-10 absolute right-0 top-0">
                            3
                        </span>
                    </a>

                    <!-- Backdrop + Modal teleportados -->
                    <template x-teleport="body">
                        <div x-show="open" style="display:none;">
                            <!-- Backdrop -->
                            <div class="fixed inset-0 z-[9998] bg-black/20" @click="open=false"></div>

                            <!-- Modal -->
                            <div
                                class="fixed z-[9999] w-[400px] max-w-[92vw] rounded-xl overflow-hidden bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-800"
                                :style="`top:${top}px; left:${left}px;`"
                                @resize.window="place()"
                                @scroll.window="place()"
                            >
                                <!-- Header -->
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-800">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ __('navbar.notifications') }}
                                    </h3>

                                    <button
                                        type="button"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        {{ __('navbar.clear_all_notifications') }}
                                    </button>
                                </div>

                                <!-- Body -->
                                <div class="p-3 max-h-[60vh] overflow-y-auto space-y-2">
                                    <!-- Card  -->
                                    <div class="rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 p-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                                    Novo arquivo na fila
                                                </p>
                                                <p class="mt-1 text-xs text-gray-600 dark:text-gray-300">
                                                    Um novo arquivo foi enviado e está aguardando processamento.
                                                </p>
                                                <p class="mt-2 text-[11px] text-gray-500 dark:text-gray-400">
                                                    2 dias atrás • 09:12
                                                </p>
                                            </div>

                                            <div class="flex flex-col items-end gap-2 shrink-0">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold
                                                   bg-gray-100 hover:bg-gray-200 text-gray-800
                                                   dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100"
                                                >
                                                    <i class="ph ph-check-circle"></i>
                                                    {{ __('buttons.mark_as_read') }}
                                                </button>

                                                <!-- Download -->
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold
                                                   bg-gray-100 hover:bg-gray-200 text-gray-800
                                                   dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-100"
                                                >
                                                    <i class="ph ph-download-simple"></i>
                                                    {{ __('buttons.download') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 text-center">
                                    <button
                                        type="button"
                                        class="text-xs text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                                        @click="open=false"
                                    >
                                        {{ __("buttons.close") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </li>

                <li class="pc-h-item header-user-profile"
                    x-data="{
                        open:false,
                        top: 0,
                        left: 0,
                        place() {
                            if (!this.$refs.btn) return;
                            const r = this.$refs.btn.getBoundingClientRect();
                            this.top  = r.bottom + 10;
                            this.left = Math.min(window.innerWidth - 340, r.right - 320);
                        }
                    }"
                    @keydown.escape.window="open=false"
                >
                    <a href="#"
                       x-ref="btn"
                       class="pc-head-link"
                       @click.prevent="open = !open; if(open){ $nextTick(()=>place()) }">
                        <i class="ph ph-user-circle"></i>
                    </a>

                    <template x-teleport="body">
                        <div x-show="open" style="display:none;">
                            <div
                                class="fixed inset-0 z-[9998] bg-black/20"
                                @click="open=false"
                            ></div>

                            <div
                                x-transition.origin.top.right
                                @click.away="open=false"
                                class="fixed z-[9999] w-[320px] rounded-xl overflow-hidden bg-white dark:bg-gray-900 shadow-xl border border-gray-200 dark:border-gray-800"
                                :style="`top:${top}px; left:${left}px;`"
                                @resize.window="place()"
                                @scroll.window="place()"
                            >
                                <div class="px-5 py-4 bg-primary-500">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('assets/images/user/avatar-2.jpg') }}" alt="user-image" class="w-10 h-10 rounded-full" />
                                        <div class="text-white">
                                            <div class="font-semibold leading-tight">{{ auth()->user()->name }}</div>
                                            <div class="text-sm opacity-90">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <a href="{{ route('profile') }}"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <i class="ph ph-user"></i>
                                        <span>{{ __('navbar.my_profile') }}</span>
                                    </a>

                                    <a href="#"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <i class="ph ph-lock-key"></i>
                                        <span>{{ __('navbar.change_password') }}</span>
                                    </a>

                                    <div class="p-2">
                                        <button
                                            type="button"
                                            wire:click.prevent="logout"
                                            class="w-full bg-primary-500 hover:bg-primary-600 flex items-center justify-center gap-2"
                                        >
                                            <i class="ph ph-sign-out"></i>
                                            <span>{{ __('navbar.logout') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </li>

            </ul>
        </div>
    </div>
</header>
