<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sentinel IA')</title>
    <script src="{{ asset('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="favicon" href="{{ asset('logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- ================= NAVIGATION ================= -->
    <header class="navbar">
        <div class="container nav-container">
            <a href="{{ route('accueil') }}" class="brand-logo">SENTINEL<span class="ia-text">IA</span></a>

            <!-- Bouton Burger (CSS/JS Vanilla) -->
            <button class="hamburger" id="hamburgerBtn" aria-label="Ouvrir le menu">
                <span></span><span></span><span></span>
            </button>

            <nav>
                <ul class="nav-menu" id="navMenu">
                    <!-- 1. Toujours visibles -->
                    <li><a href="{{ route('accueil') }}" class="nav-link">Accueil</a></li>
                    <li><a href="{{ route('base-collaborative.index') }}" class="nav-link">Rechercher</a></li>
                    <li><a href="{{ route('alertes.index') }}" class="nav-link">Alertes</a></li>
                    <li><a href="{{ route('statistiques.index') }}" class="nav-link">Statistiques</a></li>

                    @auth
                        <!-- 2. Visibles uniquement si connecté -->
                        <li><a href="{{ route('analyses.create') }}" class="nav-link">Analyser un contenu</a></li>
                        <li><a href="{{ route('signalements.create') }}" class="nav-link">Signaler une arnaque</a></li>
                        <li><a href="{{ route('signalements.index') }}" class="nav-link">Mes signalements</a></li>
                        
                        <!-- Profil scindé en deux liens clairs -->
                        <li><a href="{{ route('profile.historique') }}" class="nav-link">Mon historique</a></li>
                        <li><a href="{{ route('profile.edit') }}" class="nav-link">Paramètres du profil</a></li>

                        <!-- 3. Visibles uniquement pour les Modérateurs -->
                        @if(Auth::user()->estModerateur())
                            <li><a href="{{ route('moderation.index') }}" class="nav-link mod-link">Modération</a></li>
                            <li><a href="{{ route('alertes.create') }}" class="nav-link mod-link">Publier une alerte</a></li>
                        @endif

                        <!-- 4. Visibles uniquement pour les Administrateurs -->
                        @if(Auth::user()->estAdministrateur())
                            <li><a href="{{ route('admin.utilisateurs.index') }}" class="nav-link admin-link">Administration</a></li>
                        @endif

                        <!-- Bouton Déconnexion -->
                        <li>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-logout">Déconnexion</button>
                            </form>
                        </li>
                    @else
                        <!-- 5. Visibles uniquement si déconnecté -->
                        <li><a href="{{ route('login') }}" class="nav-link">Connexion</a></li>
                        <li><a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <!-- ================= ALERTES & MESSAGES ================= -->
    <div class="container messages-container" style="margin-top: 20px;">
        <!-- Messages Flash de succès -->
        @if (session('status'))
            <div class="alert-success fade-in">
                {{ session('status') }}
            </div>
        @endif

        <!-- Erreurs de validation globales -->
        @if ($errors->any())
            <div class="alert-error fade-in">
                <ul>
                    @foreach ($errors->all() as $erreur)
                        <li>{{ $erreur }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- ================= CONTENU DYNAMIQUE ================= -->
    <main class="main-content container">
        @yield('content')
    </main>

    <!-- ================= SCRIPTS ================= -->
    <script>
        // Logique Vanilla JS pour le menu Burger
        document.addEventListener('DOMContentLoaded', () => {
            const burgerToggle = document.getElementById('burgerToggle');
            const navMenu = document.getElementById('navMenu');

            if (burgerToggle && navMenu) {
                burgerToggle.addEventListener('click', () => {
                    burgerToggle.classList.toggle('active');
                    navMenu.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>