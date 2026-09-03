/**
 * SENTINEL IA - Moteur d'interactivité UI
 * - Navigation responsive avec Glassmorphism
 * - Double panneau Login / Register avec slogan à gauche et formulaire à droite
 * - Écran de chargement haute définition pour l'analyse IA (avec gestion du retour arrière bfcache)
 */

document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initAuthDualPanel();
    initAiLoadingScreen();
});

/* ==========================================================================
   1. NAVIGATION & MENU BURGER (GLASSMORPHISM)
   ========================================================================== */
function initNavigation() {
    const btn = document.getElementById('hamburgerBtn');
    const menu = document.getElementById('navMenu');

    if (!btn || !menu) return;

    // Bascule de l'affichage du menu
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const estOuvert = menu.classList.toggle('active');
        btn.classList.toggle('active');
        btn.setAttribute('aria-expanded', estOuvert ? 'true' : 'false');
    });

    // Fermeture lors du clic sur un lien de navigation
    menu.querySelectorAll('.nav-link, .btn').forEach((lien) => {
        lien.addEventListener('click', () => {
            menu.classList.remove('active');
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        });
    });

    // Fermeture en cliquant en dehors du menu sur mobile
    document.addEventListener('click', (e) => {
        if (menu.classList.contains('active') && !menu.contains(e.target) && !btn.contains(e.target)) {
            menu.classList.remove('active');
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    // Fermeture avec la touche Échap
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menu.classList.contains('active')) {
            menu.classList.remove('active');
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        }
    });

    // Réinitialisation lors du redimensionnement vers écran large
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && menu.classList.contains('active')) {
            menu.classList.remove('active');
            btn.classList.remove('active');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
}

/* ==========================================================================
   2. DOUBLE PANNEAU AUTH (SLOGAN À GAUCHE, FORMULAIRE À DROITE)
   ========================================================================== */
function initAuthDualPanel() {
    const authContainer = document.querySelector('.auth-container');
    if (!authContainer) return;

    const authCard = authContainer.querySelector('.auth-card');
    if (!authCard) return;

    // Détection de la page courante (Inscription vs Connexion)
    const isRegister = !!authCard.querySelector('input[name="password_confirmation"]') || 
                       window.location.pathname.includes('inscription');

    // Création de l'enveloppe double panneau
    const dualWrapper = document.createElement('div');
    dualWrapper.className = 'auth-dual-wrapper fade-in';

    // Construction du panneau Slogan Sentinel IA (toujours positionné à gauche)
    const sloganPanel = document.createElement('div');
    sloganPanel.className = 'auth-slogan-panel';

    if (!isRegister) {
        // --- Slogan pour la page de CONNEXION ---
        sloganPanel.innerHTML = `
            <span class="auth-slogan-tag">Plateforme Nationale</span>
            <h2 class="auth-slogan-title">Bouclier Numérique Sentinel IA</h2>
            <p class="auth-slogan-desc">
                Dispositif intelligent de prévention, détection des arnaques et sécurisation des transactions numériques au Cameroun.
            </p>
            <ul class="auth-slogan-features">
                <li class="auth-slogan-feature-item">
                    <span class="auth-slogan-dot"></span>
                    <span>Analyse prédictive de SMS, liens web, identifiants et numéros suspects.</span>
                </li>
                <li class="auth-slogan-feature-item">
                    <span class="auth-slogan-dot"></span>
                    <span>Base collaborative nationale alimentée par des signalements vérifiés.</span>
                </li>
                <li class="auth-slogan-feature-item">
                    <span class="auth-slogan-dot"></span>
                    <span>Diffusion en temps réel des alertes de modération et alertes de vigilance.</span>
                </li>
            </ul>
        `;
    } else {
        // --- Slogan pour la page d'INSCRIPTION ---
        sloganPanel.innerHTML = `
            <span class="auth-slogan-tag">Espace Sécurisé</span>
            <h2 class="auth-slogan-title">Rejoignez la Sentinelle</h2>
            <p class="auth-slogan-desc">
                Participez activement à la protection de vos proches et contribuez à l'assainissement de notre écosystème numérique.
            </p>
            <ul class="auth-slogan-features">
                <li class="auth-slogan-feature-item">
                    <span class="auth-slogan-dot"></span>
                    <span>Accédez en un clic à l'analyse algorithmique de contenus suspects.</span>
                </li>
                <li class="auth-slogan-feature-item">
                    <span class="auth-slogan-dot"></span>
                    <span>Déclarez les tentatives de fraude et suivez la modération en direct.</span>
                </li>
                <li class="auth-slogan-feature-item">
                    <span class="auth-slogan-dot"></span>
                    <span>Bénéficiez d'un historique complet et téléchargez vos rapports officiels.</span>
                </li>
            </ul>
        `;
    }

    // Réorganisation : slogan à gauche, formulaire à droite (pas de doublon de lien)
    authCard.parentNode.insertBefore(dualWrapper, authCard);
    dualWrapper.appendChild(sloganPanel);
    dualWrapper.appendChild(authCard);
}

/* ==========================================================================
   3. ÉCRAN DE CHARGEMENT HAUTE DÉFINITION POUR L'ANALYSE IA
   ========================================================================== */
let aiProgressInterval = null;
let aiTimeoutFallback = null;

// Réinitialisation complète du loader (notamment lors du retour en arrière du navigateur)
function resetAiLoadingScreen() {
    const overlay = document.getElementById('sentinelLoadingOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
    }

    if (aiProgressInterval) {
        clearInterval(aiProgressInterval);
        aiProgressInterval = null;
    }

    if (aiTimeoutFallback) {
        clearTimeout(aiTimeoutFallback);
        aiTimeoutFallback = null;
    }

    const statusBar = document.getElementById('sentinelLoadingBar');
    if (statusBar) {
        statusBar.style.width = '15%';
    }

    const statusText = document.getElementById('sentinelLoadingStatus');
    if (statusText) {
        statusText.textContent = "Initialisation du modèle d'analyse...";
    }

    // Réactivation immédiate de tous les boutons de soumission
    const submitButtons = document.querySelectorAll('button[type="submit"], input[type="submit"]');
    submitButtons.forEach((btn) => {
        btn.disabled = false;
        btn.style.opacity = '';
        btn.style.cursor = '';
    });
}

function initAiLoadingScreen() {
    // Réinitialisation préventive au chargement
    resetAiLoadingScreen();

    // Détection des formulaires d'analyse IA
    const aiForms = document.querySelectorAll(
        'form[action*="analyses"], form[action*="suggestion-ia"]'
    );

    if (aiForms.length === 0) return;

    // Création de la structure du modal de chargement si non présent
    let overlay = document.getElementById('sentinelLoadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'sentinelLoadingOverlay';
        overlay.className = 'sentinel-loading-overlay';
        overlay.setAttribute('aria-hidden', 'true');
        overlay.innerHTML = `
            <div class="sentinel-loading-backdrop"></div>
            <div class="sentinel-loading-modal">
                <div class="sentinel-loader-icon">
                    <div class="sentinel-spinner-ring"></div>
                    <div class="sentinel-spinner-core"></div>
                </div>
                <div class="sentinel-loading-header">
                    <span class="sentinel-loading-badge">Traitement Sécurisé</span>
                    <h3 class="sentinel-loading-title">Analyse Sentinel IA en cours</h3>
                </div>
                <div class="sentinel-loading-bar-wrap">
                    <div class="sentinel-loading-bar-fill" id="sentinelLoadingBar"></div>
                </div>
                <p class="sentinel-loading-status" id="sentinelLoadingStatus">Initialisation du modèle d'analyse...</p>
                <div class="sentinel-loading-meta">
                    <span>Vérification Heuristique</span>
                    <span>Base Nationale</span>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    const statusBar = document.getElementById('sentinelLoadingBar');
    const statusText = document.getElementById('sentinelLoadingStatus');

    // Étapes de progression techniques
    const steps = [
        { progress: '22%', text: 'Initialisation du moteur d\'analyse Sentinel IA...' },
        { progress: '48%', text: 'Extraction des signatures et analyse syntaxique...' },
        { progress: '74%', text: 'Interrogation de la base nationale des signalements...' },
        { progress: '92%', text: 'Calcul de l\'indice de risque et finalisation du rapport...' }
    ];

    aiForms.forEach((form) => {
        form.addEventListener('submit', () => {
            // Validation native des champs obligatoires avant affichage
            if (!form.checkValidity()) {
                return;
            }

            // Désactivation des boutons de soumission pour éviter les doubles envois
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach((btn) => {
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.style.cursor = 'wait';
            });

            // Affichage de l'overlay de chargement
            overlay.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');

            let currentStep = 0;
            const updateProgress = () => {
                if (currentStep < steps.length) {
                    if (statusBar) statusBar.style.width = steps[currentStep].progress;
                    if (statusText) statusText.textContent = steps[currentStep].text;
                    currentStep++;
                }
            };

            updateProgress();
            aiProgressInterval = setInterval(() => {
                if (currentStep < steps.length) {
                    updateProgress();
                } else {
                    clearInterval(aiProgressInterval);
                }
            }, 1000);

            // Sécurité de secours : réinitialiser après 30 secondes en cas d'erreur réseau
            aiTimeoutFallback = setTimeout(() => {
                resetAiLoadingScreen();
            }, 30000);
        });
    });
}

// Interception des événements de navigation du navigateur (bouton retour / bfcache)
// Lorsque l'utilisateur clique sur « Précédent », pageshow se déclenche même si la page vient du cache
window.addEventListener('pageshow', (event) => {
    resetAiLoadingScreen();
});

window.addEventListener('pagehide', () => {
    resetAiLoadingScreen();
});
