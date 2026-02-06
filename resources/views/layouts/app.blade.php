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
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="{{ asset('css/react-index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/react-app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/table-fixes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar-enhancements.css') }}">

    @stack('styles')
</head>

<body>
    <div class="appShell">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebarHeader">
                <div class="sidebarBrand">
                    <div class="sidebarBrandIcon">
                        <span class="material-symbols-outlined">account_balance</span>
                    </div>
                    <div class="sidebarBrandText">
                        <div class="sidebarTitle">SIC Compta</div>
                        <div class="sidebarSubtitle">Système Comptable</div>
                    </div>
                </div>
            </div>

            <nav class="sideNav">
                <a href="{{ route('dashboard') }}"
                    class="sideNavLink {{ request()->routeIs('dashboard') ? 'sideNavLinkActive' : '' }}">
                    <div class="sideNavIcon">
                        <span class="material-symbols-outlined">dashboard</span>
                    </div>
                    <div class="sideNavLabel">Tableau de bord</div>
                </a>

                <a href="{{ route('journals.new') }}"
                    class="sideNavLink {{ request()->routeIs('journals.new') ? 'sideNavLinkActive' : '' }}">
                    <div class="sideNavIcon">
                        <span class="material-symbols-outlined">add_circle</span>
                    </div>
                    <div class="sideNavLabel">Nouveau journal</div>
                </a>

                <a href="{{ route('journals.index') }}"
                    class="sideNavLink {{ request()->routeIs('journals.*') && !request()->routeIs('journals.new') ? 'sideNavLinkActive' : '' }}">
                    <div class="sideNavIcon">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <div class="sideNavLabel">Historique</div>
                </a>

                @if (auth()->user()->is_admin ?? false)
                    <div class="sideNavDivider"></div>
                    <div class="sideNavSection">Administration</div>

                    <a href="{{ route('users.index') }}"
                        class="sideNavLink {{ request()->routeIs('users.*') ? 'sideNavLinkActive' : '' }}">
                        <div class="sideNavIcon">
                            <span class="material-symbols-outlined">group</span>
                        </div>
                        <div class="sideNavLabel">Utilisateurs</div>
                    </a>
                @endif
            </nav>

            <div class="sidebarFooter">
                <div class="sidebarUser">
                    <div class="sidebarAvatar">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div class="sidebarUserInfo">
                        <div class="sidebarUserLabel">{{ auth()->user()->name }}</div>
                        <div class="sidebarUserRole">
                            @if (auth()->user()->is_admin)
                                <span class="material-symbols-outlined"
                                    style="font-size: 12px;">admin_panel_settings</span>
                                Administrateur
                            @else
                                <span class="material-symbols-outlined" style="font-size: 12px;">person</span>
                                Utilisateur
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin-top: 8px;">
                    @csrf
                    <button type="submit" class="sideNavLink sideNavLinkLogout">
                        <div class="sideNavIcon">
                            <span class="material-symbols-outlined">logout</span>
                        </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Debug clic sur les boutons "Supprimer"
            document.querySelectorAll('[data-debug-delete-btn]').forEach(function (btn) {
                btn.addEventListener('click', function (event) {
                    const form = btn.closest('form');
                    if (!form) return;

                    console.log('[DEBUG DELETE][click]', {
                        context: btn.getAttribute('data-debug-delete-btn'),
                        action: form.getAttribute('action'),
                        method: form.getAttribute('method'),
                        spoofedMethod: form.querySelector('input[name="_method"]')?.value || null,
                        journalId: form.dataset.journalId || null,
                        journalName: form.dataset.journalName || null,
                        numeroOperation: form.dataset.numeroOperation || null,
                        userId: form.dataset.userId || null,
                        userName: form.dataset.userName || null,
                    });
                });
            });

            // Debug juste avant l'envoi du formulaire (après le confirm)
            document.querySelectorAll('form[data-debug-delete]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    console.log('[DEBUG DELETE][submit]', {
                        context: form.getAttribute('data-debug-delete'),
                        action: form.getAttribute('action'),
                        method: form.getAttribute('method'),
                        spoofedMethod: form.querySelector('input[name=\"_method\"]')?.value || null,
                        journalId: form.dataset.journalId || null,
                        journalName: form.dataset.journalName || null,
                        numeroOperation: form.dataset.numeroOperation || null,
                        userId: form.dataset.userId || null,
                        userName: form.dataset.userName || null,
                    });
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
