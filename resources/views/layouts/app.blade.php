<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sentinel IA')</title>
    <script src="{{ asset('js/confirm.js') }}?v=2"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <a class="skip-link" href="#contenu-principal">Aller au contenu</a>

    <header class="navbar">
        <div class="container nav-container">
            <a href="{{ route('accueil') }}" class="brand-logo">SENTINEL<span class="ia-text">IA</span></a>
            <button class="hamburger" id="hamburgerBtn" aria-label="Ouvrir le menu" aria-expanded="false"><span></span><span></span><span></span></button>
            <nav aria-label="Navigation principale">
                <ul class="nav-menu" id="navMenu">
                    <li><a href="{{ route('accueil') }}" class="nav-link">Accueil</a></li>
                    <li><a href="{{ route('base-collaborative.index') }}" class="nav-link">Rechercher</a></li>
                    <li><a href="{{ route('alertes.index') }}" class="nav-link">Alertes</a></li>
                    <li><a href="{{ route('statistiques.index') }}" class="nav-link">Statistiques</a></li>
                    @auth
                        <li class="nav-dropdown">
                            <button type="button" class="nav-dropdown-trigger" aria-expanded="false">Agir <span>⌄</span></button>
                            <div class="nav-dropdown-menu">
                                <a href="{{ route('analyses.create') }}" class="nav-link">Analyser un contenu</a>
                                <a href="{{ route('signalements.create') }}" class="nav-link">Signaler une arnaque</a>
                                <a href="{{ route('signalements.index') }}" class="nav-link">Mes signalements</a>
                            </div>
                        </li>
                        <li class="nav-dropdown">
                            <button type="button" class="nav-dropdown-trigger" aria-expanded="false">Suivre <span>⌄</span></button>
                            <div class="nav-dropdown-menu">
                                <a href="{{ route('profile.historique') }}" class="nav-link">Mon historique</a>
                                <a href="{{ route('profile.edit') }}" class="nav-link">Paramètres du profil</a>
                            </div>
                        </li>
                        <li class="nav-dropdown">
                            <button type="button" class="nav-dropdown-trigger" aria-expanded="false">Piloter <span>⌄</span></button>
                            <div class="nav-dropdown-menu">
                                @if(Auth::user()->estModerateur())
                                    <a href="{{ route('moderation.index') }}" class="nav-link mod-link">Modération</a>
                                    <a href="{{ route('alertes.create') }}" class="nav-link mod-link">Publier une alerte</a>
                                @endif
                                @if(Auth::user()->estAdministrateur())
                                    <a href="{{ route('admin.utilisateurs.index') }}" class="nav-link admin-link">Administration</a>
                                @endif
                                @if(!Auth::user()->estModerateur() && !Auth::user()->estAdministrateur())
                                    <span class="nav-dropdown-empty">Espace personnel</span>
                                @endif
                            </div>
                        </li>
                        <li><form action="{{ route('logout') }}" method="POST" class="logout-form">@csrf<button type="submit" class="btn btn-logout">Déconnexion</button></form></li>
                    @else
                        <li><a href="{{ route('login') }}" class="nav-link">Connexion</a></li>
                        <li><a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <div class="container messages-container">
        @if (session('status'))<div class="alert-success fade-in">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="alert-error fade-in"><ul>@foreach ($errors->all() as $erreur)<li>{{ $erreur }}</li>@endforeach</ul></div>@endif
    </div>

    <main id="contenu-principal" class="main-content container">@yield('content')</main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <div><a href="{{ route('accueil') }}" class="brand-logo footer-brand">SENTINEL<span class="ia-text">IA</span></a><p class="footer-note">La vigilance numérique, pensée pour le Cameroun.</p></div>
            <div class="footer-links"><a href="{{ route('base-collaborative.index') }}">Base collaborative</a><a href="{{ route('alertes.index') }}">Alertes</a><a href="{{ route('statistiques.index') }}">Statistiques</a></div>
            <p class="footer-copyright">© {{ date('Y') }} MONTHE AHMED, Sentinel IA</p>
        </div>
    </footer>
</body>
</html>
