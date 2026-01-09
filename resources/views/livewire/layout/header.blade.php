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
                <livewire:layout.header-notifications />

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
                                    <a href="{{ route('profile.profile') }}"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <i class="ph ph-user"></i>
                                        <span>{{ __('navbar.my_profile') }}</span>
                                    </a>

                                    <a href="{{ route('profile.message') }}"
                                       class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <i class="ph ph-notification"></i>
                                        <span>{{ __('navbar.my_notification') }}</span>
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
