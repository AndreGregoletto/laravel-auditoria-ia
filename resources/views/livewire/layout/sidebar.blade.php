<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header flex items-center py-4 px-6 h-header-height">
            <a href="#!" class="b-brand flex items-center gap-3">
                <span class="text-white">{{ __('navbar.system_name') }}</span>
                <div class="hidden sm:flex sm:items-center sm:ms-2">
                    <livewire:language-switcher />
                </div>
            </a>
        </div>
        <div class="navbar-content h-[calc(100vh_-_74px)] py-2.5">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label data-i18n="Navigation">{{ __('navbar.tools') }}</label>
                </li>
                <li class="pc-item">
                    <a href="{{ route('dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ph ph-house-line"></i>
                        </span>
                        <span class="pc-mtext" data-i18n="Dashboard">{{ __('navbar.dashboard') }}</span>
                    </a>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"> <i class="ph ph-file-text"></i> </span>
                        <span class="pc-mtext" data-i18n="Menu levels">{{ __('navbar.import_queue') }}</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ route('balancete') }}" data-i18n="Level 2.1">{{ __('navbar.balance') }}</a></li>
                    </ul>
                </li>

                <li class="pc-item pc-caption">
                    <label data-i18n="{{ __('navbar.reports') }}">{{ __('navbar.reports') }}</label>
                </li>
                <li class="pc-item">
                    <a href="{{ route('imports.my-files') }}"  class="pc-link">
                        <span class="pc-micon"><i class="ph ph-file"></i></span>
                        <span class="pc-mtext">{{ __('navbar.my_uploaded_files') }}</span>
                    </a>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"> <i class="ph ph-building"></i> </span>
                        <span class="pc-mtext">{{ __('navbar.company') }}</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ route('reports.companies.tree') }}" data-i18n="Level 3.1">{{ __('navbar.company_tree') }}</a></li>
                    </ul>
                </li>
                <li class="pc-item pc-caption">
                    <label data-i18n="{{ __('navbar.settings') }}">{{ __('navbar.settings') }}</label>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"> <i class="ph ph-building"></i> </span>
                        <span class="pc-mtext">{{ __('navbar.company') }}</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ route('settings.companies.index') }}" data-i18n="Level 4.1">{{ __('navbar.consult') }}</a></li>
                        <li class="pc-item"><a class="pc-link" href="{{ route('settings.companies.create') }}" data-i18n="Level 4.1">{{ __('navbar.new') }}</a></li>
                    </ul>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon"> <i class="ph ph-tree-structure"></i> </span>
                        <span class="pc-mtext">{{ __('navbar.company_tree') }}</span>
                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ route('settings.companies_tree.index') }}" data-i18n="Level 5.1">{{ __('navbar.consult') }}</a></li>
                        <li class="pc-item"><a class="pc-link" href="{{ route('settings.companies_tree.create') }}" data-i18n="Level 5.1">{{ __('navbar.new') }}</a></li>
                    </ul>
                </li>

                <li class="pc-item pc-caption">
                    <label data-i18n="Other">Other</label>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link"
                    ><span class="pc-micon"> <i class="ph ph-tree-structure"></i> </span><span class="pc-mtext" data-i18n="Menu levels">Menu levels</span
                        ><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span
                        ></a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 2.1">Level 2.1</a></li>
                        <li class="pc-item pc-hasmenu">
                            <a href="#!" class="pc-link">
                                <span data-i18n="Level 2.2">Level 2.2</span>
                                <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span
                                ></a>
                            <ul class="pc-submenu">
                                <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 6.1">Level 3.1</a></li>
                                <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 6.2">Level 3.2</a></li>
                                <li class="pc-item pc-hasmenu">
                                    <a href="#!" class="pc-link">
                                        <span data-i18n="Level 3.3">Level 3.3</span>
                                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                                    </a>
                                    <ul class="pc-submenu">
                                        <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 7.1">Level 4.1</a></li>
                                        <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 7.2">Level 4.2</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="pc-item pc-hasmenu">
                            <a href="#!" class="pc-link">
                                <span data-i18n="Level 2.3">Level 2.3</span>
                                <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span
                                ></a>
                            <ul class="pc-submenu">
                                <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 3.1">Level 3.1</a></li>
                                <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 3.2">Level 3.2</a></li>
                                <li class="pc-item pc-hasmenu">
                                    <a href="#!" class="pc-link">
                                        <span data-i18n="Level 3.3">Level 3.3</span>
                                        <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
                                    </a>
                                    <ul class="pc-submenu">
                                        <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 4.1">Level 4.1</a></li>
                                        <li class="pc-item"><a class="pc-link" href="#!" data-i18n="Level 4.2">Level 4.2</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="pc-item">
                    <a href="../other/sample-page.html" class="pc-link">
                        <span class="pc-micon">
                          <i class="ph ph-desktop"></i>
                        </span>
                        <span class="pc-mtext" data-i18n="Sample Page">Sample page</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ Main Content ] end -->

