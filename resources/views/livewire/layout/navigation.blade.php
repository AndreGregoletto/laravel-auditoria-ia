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
}; ?>

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate>
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                <div class="hidden sm:flex sm:-my-px sm:ms-10">
                    <div x-data="{ openImport: false }" class="relative flex items-center">

                        <button
                            @click="openImport = !openImport"
                            class="flex items-center gap-x-1 whitespace-nowrap text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        >
                            {{ __('Fila de Importação') }}

                            <svg viewBox="0 0 20 20" fill="currentColor"
                                 class="size-5 flex-none text-gray-400 transition-transform"
                                 :class="{ 'rotate-180': openImport }">
                                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4 4a.75.75 0 0 1-1.06 0l-4-4a.75.75 0 0 1 0-1.06Z"/>
                            </svg>
                        </button>

                        <div
                            x-show="openImport"
                            x-transition.opacity
                            @click.outside="openImport = false"
                            class="absolute **top-full mt-5 left-0** z-50 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-lg border
                            border-gray-200 dark:border-gray-700"
                            style="margin-top: 125px"
                        >
                            <div class="p-4">

                                <div class="group relative flex items-center gap-x-4 rounded-lg p-4 text-base hover:bg-gray-50 dark:hover:bg-gray-900 cursor-pointer">

                                    <div class="flex size-11 flex-none items-center justify-center rounded-lg bg-gray-50 dark:bg-gray-700 group-hover:bg-white">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                             stroke-width="1.5"
                                             class="size-6 text-gray-600 dark:text-gray-300 group-hover:text-indigo-600">
                                            <path d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z"
                                                  stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>

                                    <div class="w-full">
                                        <a href="#" wire:navigate class="font-semibold text-gray-900 dark:text-gray-200 **block**">
                                            Balancete
                                        </a>
                                        <p class="text-gray-600 dark:text-gray-400 text-sm w-full max-w-9/10"  >
                                            Enviar Balancete para a fila.
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>


                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('excel')" :active="request()->routeIs('excel')" wire:navigate>
                        {{ __('Excel') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
<!-- [ Header Topbar ] start -->
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
                            <input type="search" class="form-control !border-0 !shadow-none" placeholder="Search here. . ." />
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="inline-flex *:min-h-header-height *:inline-flex *:items-center">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ph ph-diamonds-four"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        <a href="#!" class="dropdown-item">
                            <i class="ph ph-user"></i>
                            <span>My Account</span>
                        </a>
                        <a href="#!" class="dropdown-item">
                            <i class="ph ph-gear"></i>
                            <span>Settings</span>
                        </a>
                        <a href="#!" class="dropdown-item">
                            <i class="ph ph-lifebuoy"></i>
                            <span>Support</span>
                        </a>
                        <a href="#!" class="dropdown-item">
                            <i class="ph ph-lock-key"></i>
                            <span>Lock Screen</span>
                        </a>
                        <a href="#!" class="dropdown-item">
                            <i class="ph ph-power"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </li>
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle me-0" data-pc-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <i class="ph ph-bell"></i>
                        <span class="badge bg-success-500 text-white rounded-full z-10 absolute right-0 top-0">3</span>
                    </a>
                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown p-2">
                        <div class="dropdown-header flex items-center justify-between py-4 px-5">
                            <h5 class="m-0">Notifications</h5>
                            <a href="#!" class="btn btn-link btn-sm">Mark all read</a>
                        </div>
                        <div class="dropdown-body header-notification-scroll relative py-4 px-5"
                             style="max-height: calc(100vh - 215px)">
                            <p class="text-span mb-3">Today</p>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="flex gap-4">
                                        <div class="shrink-0">
                                            <img class="img-radius w-12 h-12 rounded-0" src="../assets/images/user/avatar-1.jpg" alt="Generic placeholder image" />
                                        </div>
                                        <div class="grow">
                                            <span class="float-end text-sm text-muted">2 min ago</span>
                                            <h5 class="text-body mb-2">UI/UX Design</h5>
                                            <p class="mb-0">
                                                Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                                                printer took a galley of
                                                type and scrambled it to make a type
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-span mb-3 mt-4">Yesterday</p>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <div class="flex gap-4">
                                        <div class="shrink-0">
                                            <img class="img-radius w-12 h-12 rounded-0" src="../assets/images/user/avatar-3.jpg" alt="Generic placeholder image" />
                                        </div>
                                        <div class="grow ms-3">
                                            <span class="float-end text-sm text-muted">2 hour ago</span>
                                            <h5 class="text-body mb-2">Forms</h5>
                                            <p class="mb-0">
                                                Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                                                printer took a galley of
                                                type and scrambled it to make a type
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center py-2">
                            <a href="#!" class="text-danger-500 hover:text-danger-600 focus:text-danger-600 active:text-danger-600">
                                Clear all Notifications
                            </a>
                        </div>
                    </div>
                </li>
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-pc-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-pc-auto-close="outside" aria-expanded="false">
                        <i class="ph ph-user-circle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown p-2 overflow-hidden">
                        <div class="dropdown-header flex items-center justify-between py-4 px-5 bg-primary-500">
                            <div class="flex mb-1 items-center">
                                <div class="shrink-0">
                                    <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="w-10 rounded-full" />
                                </div>
                                <div class="grow ms-3">
                                    <h6 class="mb-1 text-white">Carson Darrin 🖖</h6>
                                    <span class="text-white">carson.darrin@company.io</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-body py-4 px-5">
                            <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                <a href="#" class="dropdown-item">
              <span>
                <i class="ph ph-gear align-middle me-2"></i>
                <span>Settings</span>
              </span>
                                </a>
                                <a href="#" class="dropdown-item">
              <span>
                <i class="ph ph-share-network align-middle me-2"></i>
                <span>Share</span>
              </span>
                                </a>
                                <a href="#" class="dropdown-item">
              <span>
                <i class="ph ph-lock-key align-middle me-2"></i>
                <span>Change Password</span>
              </span>
                                </a>
                                <div class="grid my-3">
                                    <button class="btn btn-primary flex items-center justify-center">
                                        <i class="ph ph-sign-out align-middle me-2"></i>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div></div>
</header>
<!-- [ Header ] end -->
