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
</head>

<body>
    <div class="wrapper">
        
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

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('contacts.index') }}">
                            <i class="align-middle" data-feather="users"></i> 
                            <span class="align-middle">Contacts</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('companies.index') }}">
                            <i class="align-middle" data-feather="briefcase"></i> 
                            <span class="align-middle">Companies</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('deals.index') }}">
                            <i class="align-middle" data-feather="dollar-sign"></i> 
                            <span class="align-middle">Deals</span>
                        </a>
                    </li>

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

                    <ul class="navbar-nav ms-auto navbar-align">

                        {{-- User Menu --}}
                        <li class="nav-item dropdown">

                            <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                <img src="{{ asset('admin/img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded me-1" alt="User" />
                                <span class="text-dark">{{ auth()->user()->name ?? 'User' }}</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">
                                    <i class="align-middle me-1" data-feather="user"></i> Profile
                                </a>

                                <div class="dropdown-divider"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">
                                        <i class="align-middle me-1" data-feather="log-out"></i>
                                        Logout
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

    @stack('scripts')
</body>
</html>
