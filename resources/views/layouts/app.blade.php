<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') · {{ config('app.name', 'Laravel') }}
        @else
            {{ config('app.name', 'Laravel') }}
        @endif
    </title>

    <!-- Fonts (optional: use any you like) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Vite compiled CSS & JS (includes Bootstrap) -->
    @vite(['resources/js/app.js'])
</head>
<body class="bg-light">
    <div id="app">
        {{-- Top Navbar --}}
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand fw-semibold" href="{{ url('/') }}">
                    {{ config('app.name', 'CRM SaaS') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    {{-- Left side links --}}
                    @auth
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    Dashboard
                                </a>
                            </li>

                            {{-- Example CRM links, adjust as routes exist --}}
                            <li class="nav-item">
                                <a href="{{ route('contacts.index') }}" class="nav-link {{ request()->is('contacts*') ? 'active' : '' }}">
                                    Contacts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('companies.index') }}" class="nav-link {{ request()->is('companies*') ? 'active' : '' }}">
                                    Companies
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deals.index') }}" class="nav-link {{ request()->is('deals*') ? 'active' : '' }}">
                                    Deals
                                </a>
                            </li>
                            {{-- Add more: Inventory, HR, etc. --}}
                        </ul>
                    @endauth

                    {{-- Right side auth / user menu --}}
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                                </li>
                            @endif
                        @else
                            {{-- Notifications / "What's New" placeholder --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="whatsNewDropdown" role="button"
                                   data-bs-toggle="dropdown" aria-expanded="false">
                                    What’s New
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="whatsNewDropdown">
                                    <li class="dropdown-item text-muted small">No new updates yet.</li>
                                </ul>
                            </li>

                            {{-- User menu --}}
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    {{-- Account/Profile --}}
                                    @if (Route::has('profile.show'))
                                        <li>
                                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                                Profile & Account
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif

                                    {{-- Logout --}}
                                    <li>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="py-4">
            <div class="container">
                {{-- Flash messages --}}
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>There were some problems with your input.</strong>
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
