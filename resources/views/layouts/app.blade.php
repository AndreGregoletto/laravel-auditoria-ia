<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:livewire="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="icon" href="{{ asset('/assets/images/favicon.svg') }}" type="image/x-icon" />

        <link rel="stylesheet" href="{{ asset('/assets/css/plugins/jsvectormap.min.css') }}" />
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('/assets/fonts/phosphor/regular/style.css') }}" />
        <link rel="stylesheet" href="{{ asset('/assets/fonts/tabler-icons.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}" id="main-style-link" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        <div class="loader-bg fixed inset-0 bg-white dark:bg-themedark-cardbg z-[1034]">
            <div class="loader-track h-[5px] w-full inline-block absolute overflow-hidden top-0">
                <div class="loader-fill w-[300px] h-[5px] bg-primary-500 absolute top-0 left-0 animate-[hitZak_0.6s_ease-in-out_infinite_alternate]"></div>
            </div>
        </div>

        <livewire:layout.sidebar />

        <livewire:layout.header />

        <div class="pc-container">
            <div class="pc-content">

                <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
                    <!-- Page Heading -->
                    @if (isset($header))
                        <header class="bg-white dark:bg-gray-800 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endif

                    <!-- Page Content -->
                    <main>
                        {{ $slot }}
                    </main>
                </div>

            </div>
        </div>


        <footer class="pc-footer">
            <div class="footer-wrapper container-fluid mx-10">
                <div class="grid grid-cols-12 gap-1.5">
                    <div class="col-span-12 md:col-span-6 my-1">
                        <p class="m-0"></p>
                        <a href="https://codedthemes.com/" class="text-theme-bodycolor dark:text-themedark-bodycolor hover:text-primary-500 dark:hover:text-primary-500" target="_blank">CodedThemes</a>
                        , Built with ♥ for a smoother web presence.
                        </p>
                    </div>
                    <div class="col-span-12 md:col-span-6 my-1">
                        <ul class="mb-0 ltr:sm:text-right rtl:sm:text-left *:text-theme-bodycolor dark:*:text-themedark-bodycolor *:hover:text-primary-500 dark:*:hover:text-primary-500">
                            <li class="inline-block max-sm:mr-2 sm:ml-2"><a href="../index.html">Home</a></li>
                            <li class="inline-block max-sm:mr-2 sm:ml-2"><a href="https://codedthemes.gitbook.io/datta/datta-able-tailwind" target="_blank">Documentation</a></li>
                            <li class="inline-block max-sm:mr-2 sm:ml-2"><a href="https://codedthemes.support-hub.io/" target="_blank">Support</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>


        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/jsvectormap.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/world.js') }}"></script>

        <!-- custom widgets js -->
        <script src="{{ asset('assets/js/widgets/world-low.js') }}"></script>
        <script src="{{ asset('assets/js/widgets/Widget-line-chart.js') }}"></script>
        <!-- [Page Specific JS] end -->
        <!-- Required Js -->
        <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
        <script src="{{ asset('assets/js/component.js') }}"></script>
        <script src="{{ asset('assets/js/theme.js') }}"></script>
        <script src="{{ asset('assets/js/script.js') }}"></script>

        <script>layout_change('false');</script>
        <script>layout_theme_sidebar_change('dark');</script>
        <script>change_box_container('false');</script>
        <script>layout_caption_change('true');</script>
        <script>layout_rtl_change('false');</script>
        <script>preset_change('preset-1');</script>
        <script>main_layout_change('vertical');</script>

    </body>

</html>
