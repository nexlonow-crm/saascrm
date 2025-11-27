<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>@yield('title', 'CRM')</title>

    <!-- AdminKit CSS -->
    <link href="{{ asset('admin/css/app.css') }}" rel="stylesheet" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    @stack('styles')

    <style>
        /* Basic dark mode overrides */
        body.theme-dark {
            background-color: #181b1f;
            color: #e9ecef;
        }

        body.theme-dark .navbar-bg {
            background-color: #21252b !important;
        }

        body.theme-dark .sidebar {
            background-color: #181b1f;
        }

        body.theme-dark .card {
            background-color: #21252b;
            color: #e9ecef;
        }

        body.theme-dark .table {
            color: #e9ecef;
        }

        body.theme-dark .table thead {
            background-color: #181b1f;
        }

        body.theme-dark a,
        body.theme-dark .text-muted {
            color: #adb5bd !important;
        }
    </style>

</head>

<body>
    <div class="wrapper">
    @php
    use Illuminate\Support\Facades\Route as RouteFacade;
    @endphp    
        {{-- SIDEBAR --}}
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">

                <a class="sidebar-brand" href="{{ route('dashboard') }}">
                    <span class="align-middle">{{ config('app.name') }}</span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">CRM</li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('dashboard') }}">
                            <i class="align-middle" data-feather="sliders"></i>
                            <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    {{-- Contacts (all plans in our config) --}}
                    @if(isset($currentTenant) && $currentTenant->hasFeature('contacts'))
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('contacts.index') }}">
                                <i class="align-middle" data-feather="users"></i>
                                <span class="align-middle">Contacts</span>
                            </a>
                        </li>
                    @endif

                    {{-- Companies --}}
                    @if(isset($currentTenant) && $currentTenant->hasFeature('companies'))
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('companies.index') }}">
                                <i class="align-middle" data-feather="briefcase"></i>
                                <span class="align-middle">Companies</span>
                            </a>
                        </li>
                    @endif

                    {{-- SALES GROUP: Deals + Pipelines --}}
                    @php
                        $showSalesGroup =
                            isset($currentTenant)
                            && $currentTenant->hasAnyFeature(['deals.basic', 'pipelines.basic']);
                    @endphp

                    @if($showSalesGroup)
                        <li class="sidebar-header">Sales</li>

                        @if($currentTenant->hasFeature('deals.basic') && RouteFacade::has('deals.index'))
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ route('deals.index') }}">
                                    <i class="align-middle" data-feather="dollar-sign"></i>
                                    <span class="align-middle">Deals</span>
                                </a>
                            </li>
                        @endif

                        @if($currentTenant->hasFeature('pipelines.basic') && RouteFacade::has('pipelines.index'))
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="{{ route('pipelines.index') }}">
                                    <i class="align-middle" data-feather="git-branch"></i>
                                    <span class="align-middle">Pipelines</span>
                                </a>
                            </li>
                        @endif
                    @endif


                    {{-- Inventory only for Pro + Enterprise --}}
                    @if(isset($currentTenant) && $currentTenant->hasFeature('inventory'))
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('inventory.index') }}">
                                <i class="align-middle" data-feather="truck"></i>
                                <span class="align-middle">Inventory</span>
                            </a>
                        </li>
                    @endif

                    {{-- HR only for Enterprise --}}
                    @if(isset($currentTenant) && $currentTenant->hasFeature('hr'))
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('hr.employees.index') }}">
                                <i class="align-middle" data-feather="users"></i>
                                <span class="align-middle">HR & Team</span>
                            </a>
                        </li>
                    @endif

                    {{-- Accounting only for Enterprise --}}
                    @if(isset($currentTenant) && $currentTenant->hasFeature('accounting'))
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="{{ route('accounting.dashboard') }}">
                                <i class="align-middle" data-feather="file-text"></i>
                                <span class="align-middle">Accounting</span>
                            </a>
                        </li>
                    @endif
                </ul>


            </div>
        </nav>

        {{-- MAIN CONTENT --}}
        <div class="main">

            {{-- Navbar --}}
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>

                <div class="navbar-collapse collapse">
                    @php
                        $navbarUser = auth()->user();
                        $unreadCount = $navbarUser?->unreadNotifications()->count() ?? 0;
                        $latestNotifications = $navbarUser
                            ? $navbarUser->notifications()->latest()->limit(5)->get()
                            : collect();
                    @endphp

                    <ul class="navbar-nav ms-auto navbar-align">
                        {{-- Notifications dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                                <div class="position-relative">
                                    <i class="align-middle" data-feather="bell"></i>
                                    @if($unreadCount > 0)
                                        <span class="indicator">{{ $unreadCount }}</span>
                                    @endif
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
                                <div class="dropdown-menu-header">
                                    {{ $unreadCount }} New Notification{{ $unreadCount === 1 ? '' : 's' }}
                                </div>
                                <div class="list-group">
                                    @forelse($latestNotifications as $notification)
                                        @php
                                            $data = $notification->data;
                                        @endphp
                                        <a href="{{ $data['url'] ?? '#' }}"
                                        class="list-group-item {{ is_null($notification->read_at) ? '' : 'bg-light' }}">
                                            <div class="row g-0 align-items-center">
                                                <div class="col-2">
                                                    <i class="text-primary"
                                                    data-feather="{{ $data['icon'] ?? 'info' }}"></i>
                                                </div>
                                                <div class="col-10">
                                                    <div class="text-dark">{{ $data['title'] ?? 'Notification' }}</div>
                                                    <div class="text-muted small mt-1">{{ $data['body'] ?? '' }}</div>
                                                    <div class="text-muted small mt-1">
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="list-group-item text-center text-muted small py-3">
                                            No notifications yet.
                                        </div>
                                    @endforelse
                                </div>
                                <div class="dropdown-menu-footer d-flex justify-content-between align-items-center">
                                    <a href="{{ route('notifications.index') }}" class="text-muted small px-3 py-2">
                                        View all
                                    </a>

                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.readAll') }}" class="px-3 py-2">
                                            @csrf
                                            <button class="btn btn-link btn-sm p-0">
                                                Mark all as read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </li>

                        {{-- 🔆 Dark mode toggle button --}}
                        <li class="nav-item">
                            <a href="#" class="nav-icon" id="theme-toggle" title="Toggle dark mode">
                                <i class="align-middle" data-feather="moon"></i>
                            </a>
                        </li>

                        {{-- User dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img src="{{ asset('admin/img/avatars/avatar.jpg') }}"
                                    class="avatar img-fluid rounded me-1" alt="User" />
                                <span class="text-dark">{{ $navbarUser->name ?? 'User' }}</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-item-text small">
                                    <div class="fw-bold">{{ $navbarUser->name }}</div>
                                    <div class="text-muted">{{ $navbarUser->email }}</div>
                                    @isset($currentAccount)
                                        <div class="text-muted">Plan: {{ ucfirst($currentAccount->plan) }}</div>
                                    @endisset
                                </div>
                                <div class="dropdown-divider"></div>

                                {{-- optional profile link if route exists --}}
                                @if(\Illuminate\Support\Facades\Route::has('profile.show'))
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="align-middle me-1" data-feather="user"></i> Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">
                                        <i class="align-middle me-1" data-feather="log-out"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </li>


                    </ul>

                </div>
            </nav>

            {{-- PAGE CONTENT --}}
            <main class="content">
                <div class="container-fluid p-0">

                    @hasSection('page-title')
                        <h1 class="h3 mb-3">@yield('page-title')</h1>
                    @endif

                    @yield('content')

                </div>
            </main>

            {{-- FOOTER --}}
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <strong>{{ config('app.name') }}</strong> © {{ date('Y') }}
                            </p>
                        </div>
                        <div class="col-6 text-end"></div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- AdminKit JS -->
    <script src="{{ asset('admin/js/app.js') }}"></script>
    <script>
        (function () {
            const STORAGE_KEY = 'crm_theme'; // light | dark
            const body = document.body;
            const toggle = document.getElementById('theme-toggle');

            // init from localStorage
            const stored = localStorage.getItem(STORAGE_KEY) || 'light';
            if (stored === 'dark') {
                body.classList.add('theme-dark');
            }

            // update icon based on current theme
            function updateIcon() {
                if (!toggle) return;
                const icon = toggle.querySelector('i');
                if (!icon || !window.feather) return;

                icon.dataset.feather = body.classList.contains('theme-dark') ? 'sun' : 'moon';
                window.feather.replace();
            }

            updateIcon();

            if (toggle) {
                toggle.addEventListener('click', function (e) {
                    e.preventDefault();
                    const isDark = body.classList.toggle('theme-dark');
                    localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
                    updateIcon();
                });
            }
        })();
    </script>


    @stack('scripts')
</body>
</html>
