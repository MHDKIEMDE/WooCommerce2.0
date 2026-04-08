<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    @php $shopName = \App\Models\Setting::get('shop_name', config('app.name')); @endphp
    <title>@yield('title', 'Dashboard') — {{ $shopName }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ URL::to('/') }}/admin.css">

    <style>
        body.sb-nav-fixed { background: #f8f9fa; }
        .sb-topnav { height: 56px; }
        #layoutSidenav { display: flex; }
        #layoutSidenav_nav { width: 225px; flex-shrink: 0; }
        #layoutSidenav_content { flex-grow: 1; min-width: 0; display: flex; flex-direction: column; min-height: 100vh; padding-top: 56px; }
        .sb-sidenav { position: fixed; top: 56px; left: 0; width: 225px; height: calc(100vh - 56px); overflow-y: auto; background: #212529; }
        .sb-sidenav .sb-sidenav-menu { padding-bottom: 1rem; }
        .sb-sidenav .nav-link { color: rgba(255,255,255,.7); padding: .7rem 1rem; font-size: .875rem; display: flex; align-items: center; gap: .5rem; }
        .sb-sidenav .nav-link:hover, .sb-sidenav .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sb-sidenav .sb-sidenav-menu-heading { padding: .75rem 1rem .25rem; font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.4); }
        .sb-sidenav .sb-sidenav-menu-nested { padding-left: 1.5rem; }
        .sb-sidenav-collapse-arrow { margin-left: auto; transition: transform .2s; }
        [aria-expanded="true"] .sb-sidenav-collapse-arrow { transform: rotate(180deg); }
        .sb-topnav .navbar-brand { font-size: 1rem; letter-spacing: .02em; }
        @media (max-width: 992px) {
            #layoutSidenav_nav { display: none; }
            .sb-sidenav { display: none; }
            .sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_nav,
            .sb-nav-fixed.sb-sidenav-toggled .sb-sidenav { display: block; }
            #layoutSidenav_content { padding-top: 56px; }
        }
    </style>
</head>

<body class="sb-nav-fixed">

    {{-- Topnav --}}
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark fixed-top">
        <a class="navbar-brand ps-3 fw-bold" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-leaf me-2 text-success"></i>
            {{ \App\Models\Setting::get('shop_name', 'Admin') }}
        </a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 text-white" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="navbar-nav ms-auto me-3">
            <li class="nav-item me-3">
                <a class="nav-link text-white" href="{{ route('home') }}" target="_blank" title="Voir la boutique">
                    <i class="fas fa-external-link-alt"></i>
                    <span class="d-none d-md-inline ms-1">Boutique</span>
                </a>
            </li>
            <li class="nav-item dropdown">
                @auth
                <a class="nav-link dropdown-toggle text-white" id="userDropdown" href="#"
                   role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i>{{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('account.profile') }}"><i class="fas fa-user me-2"></i>Mon profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
                @endauth
            </li>
        </ul>
    </nav>

    <div id="layoutSidenav">

        {{-- Sidebar --}}
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav flex-column">
                        <div class="sb-sidenav-menu-heading">Vue d'ensemble</div>
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i> Tableau de bord
                        </a>
                        <div class="sb-sidenav-menu-heading">Catalogue</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                           data-bs-target="#collapseProducts" aria-controls="collapseProducts">
                            <i class="fas fa-box-open"></i> Produits
                            <i class="fas fa-angle-down sb-sidenav-collapse-arrow"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.products.*') ? 'show' : '' }}"
                             id="collapseProducts" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav flex-column">
                                <a class="nav-link" href="{{ route('admin.products.index') }}">
                                    <i class="fas fa-list fa-xs me-1"></i> Liste des produits
                                </a>
                                <a class="nav-link" href="{{ route('admin.products.create') }}">
                                    <i class="fas fa-plus fa-xs me-1"></i> Ajouter un produit
                                </a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse"
                           data-bs-target="#collapseCategories" aria-controls="collapseCategories">
                            <i class="fas fa-tags"></i> Catégories
                            <i class="fas fa-angle-down sb-sidenav-collapse-arrow"></i>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.categories.*') ? 'show' : '' }}"
                             id="collapseCategories" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav flex-column">
                                <a class="nav-link" href="{{ route('admin.categories.index') }}">
                                    <i class="fas fa-list fa-xs me-1"></i> Liste
                                </a>
                                <a class="nav-link" href="{{ route('admin.categories.create') }}">
                                    <i class="fas fa-plus fa-xs me-1"></i> Ajouter
                                </a>
                            </nav>
                        </div>

                        <div class="sb-sidenav-menu-heading">Vitrine</div>
                        <a class="nav-link {{ request()->routeIs('admin.slides.*') ? 'active' : '' }}"
                           href="{{ route('admin.slides.index') }}">
                            <i class="fas fa-images"></i> Carrousel (Slides)
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }}"
                           href="{{ route('admin.testimonials.index') }}">
                            <i class="fas fa-comments"></i> Témoignages
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}"
                           href="{{ route('admin.promotions.index') }}">
                            <i class="fas fa-percent"></i> Promotions
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.home-settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.home-settings.edit') }}">
                            <i class="fas fa-home"></i> Bannière & Stats
                        </a>

                        <div class="sb-sidenav-menu-heading">Ventes</div>
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                           href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-cart"></i> Commandes
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}"
                           href="{{ route('admin.stock.index') }}">
                            <i class="fas fa-boxes"></i> Gestion du stock
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.delivery-zones.*') ? 'active' : '' }}"
                           href="{{ route('admin.delivery-zones.index') }}">
                            <i class="fas fa-truck"></i> Zones de livraison
                        </a>

                        <div class="sb-sidenav-menu-heading">Utilisateurs</div>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                           href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users"></i> Tous les utilisateurs
                        </a>

                        <div class="sb-sidenav-menu-heading">Paramètres</div>
                        <a class="nav-link {{ request()->routeIs('admin.shop-settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.shop-settings.edit') }}">
                            <i class="fas fa-store"></i> Boutique & Devise
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.theme-settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.theme-settings.edit') }}">
                            <i class="fas fa-palette"></i> Thème & Couleurs
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.social-settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.social-settings.edit') }}">
                            <i class="fas fa-share-alt"></i> Réseaux sociaux
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.notification-settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.notification-settings.edit') }}">
                            <i class="fab fa-whatsapp"></i> Notifications WhatsApp
                        </a>
                        <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
                           href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-bell"></i> Notifications Push
                        </a>

                    </div>
                </div>
            </nav>
        </div>

        {{-- Contenu principal --}}
        <div id="layoutSidenav_content">
            <main class="flex-grow-1">

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 mb-0" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @yield('contents')

            </main>

            <footer class="py-3 bg-light border-top mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small text-muted">
                        <span>
                            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('shop_name', config('app.name')) }}
                            — Tous droits réservés
                        </span>
                        <span>Administration</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script>
        // Sidebar toggle mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', e => {
                e.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
            });
        }
        // DataTable auto-init
        window.addEventListener('DOMContentLoaded', () => {
            const dt = document.getElementById('datatablesSimple');
            if (dt) new simpleDatatables.DataTable(dt);
        });
    </script>
    @stack('scripts')
</body>
</html>
