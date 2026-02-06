<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Comptabilité') - SIC</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/react-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/react-app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table-fixes.css') }}">

    @stack('styles')
</head>

<body>
    <div class="appShell">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebarHeader">
                <div class="sidebarTitle">💼 SIC Compta</div>
            </div>

            <nav class="sideNav">
                <a href="{{ route('dashboard') }}"
                    class="sideNavLink {{ request()->routeIs('dashboard') ? 'sideNavLinkActive' : '' }}">
                    <div class="sideNavIcon">📊</div>
                    <div class="sideNavLabel">Tableau de bord</div>
                </a>

                <a href="{{ route('journals.new') }}"
                    class="sideNavLink {{ request()->routeIs('journals.new') ? 'sideNavLinkActive' : '' }}">
                    <div class="sideNavIcon">➕</div>
                    <div class="sideNavLabel">Nouveau journal</div>
                </a>

                <a href="{{ route('journals.index') }}"
                    class="sideNavLink {{ request()->routeIs('journals.*') && !request()->routeIs('journals.new') ? 'sideNavLinkActive' : '' }}">
                    <div class="sideNavIcon">📘</div>
                    <div class="sideNavLabel">Historique</div>
                </a>

                @if (auth()->user()->is_admin ?? false)
                    <a href="{{ route('users.index') }}"
                        class="sideNavLink {{ request()->routeIs('users.*') ? 'sideNavLinkActive' : '' }}">
                        <div class="sideNavIcon">👥</div>
                        <div class="sideNavLabel">Utilisateurs</div>
                    </a>
                @endif
            </nav>

            <div class="sidebarFooter">
                <div class="sidebarUser">
                    <div class="sidebarAvatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <div class="sidebarUserLabel">{{ auth()->user()->name }}</div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin-top: 8px;">
                    @csrf
                    <button type="submit" class="sideNavLink"
                        style="width: 100%; border: none; background: none; cursor: pointer;">
                        <div class="sideNavIcon">🚪</div>
                        <div class="sideNavLabel">Déconnexion</div>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="appMain">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="success animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert animate-fade-in">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert animate-fade-in">
                    <strong>Erreurs de validation :</strong>
                    <ul style="margin: 0.5rem 0 0 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
