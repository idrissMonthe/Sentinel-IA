<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\Admin\UtilisateurController;
use App\Http\Controllers\AlerteController;
use App\Http\Controllers\AnalyseController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EntiteSuspecteController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\PreuveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatistiqueController;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('auth')->post('/signalements/suggestion-ia', [SignalementController::class, 'suggestionIA'])
    ->name('signalements.suggestion-ia');
    /*
|--------------------------------------------------------------------------
| Support — accueil (page publique, aucune logique métier)
|--------------------------------------------------------------------------
*/
Route::view('/', 'accueil')->name('accueil');

/*
|--------------------------------------------------------------------------
| Support — Authentification
| Accessible uniquement aux visiteurs NON connectés (middleware 'guest'),
| sauf la déconnexion qui exige l'inverse.
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Formulaires : simples vues, pas de méthode dédiée dans AuthController
    Route::view('/inscription', 'auth.register')->name('register');
    Route::view('/connexion', 'auth.login')->name('login');

    // Traitement
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.store');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.store');
});
Route::middleware('auth')->post('/deconnexion', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Support — Profil (Utilisateur connecté)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('profil')->name('profile.')->group(function () {
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/historique', [ProfileController::class, 'historique'])->name('historique');
});
/*
|--------------------------------------------------------------------------
| PILIER 2 — Signalement et base collaborative
|--------------------------------------------------------------------------
*/

// Rechercher dans la base collaborative : PUBLIC, sans authentification
// (rôle préventif du thème, cf. réorientation du diagramme de cas d'utilisation)
Route::prefix('base-collaborative')->name('base-collaborative.')->group(function () {
    Route::get('/', [EntiteSuspecteController::class, 'index'])->name('index');
    Route::get('/{entiteSuspecte}', [EntiteSuspecteController::class, 'show'])->name('show');
});
// Signaler une arnaque / Suivre un signalement / Fournir une preuve : AUTHENTIFIÉ
Route::middleware('auth')->prefix('signalements')->name('signalements.')->group(function () {
    Route::get('/', [SignalementController::class, 'index'])->name('index');           // Suivre un signalement
    Route::get('/creer', [SignalementController::class, 'create'])->name('create');
    Route::post('/', [SignalementController::class, 'store'])->name('store');
    Route::get('/{signalement}', [SignalementController::class, 'show'])->name('show'); // protégé par SignalementPolicy::view

    // Rédiger un signalement assisté par IA (<<extend>>, optionnel)
    Route::post('/suggestion-ia', [SignalementController::class, 'suggestionIA'])->name('suggestion-ia');

    // Fournir une preuve (<<include>>, nichée sous un signalement précis)
    Route::post('/{signalement}/preuves', [PreuveController::class, 'store'])->name('preuves.store');
});
/*
|--------------------------------------------------------------------------
| PILIER 1 — Détection et analyse intelligente
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('analyses')->name('analyses.')->group(function () {
    Route::get('/creer', [AnalyseController::class, 'create'])->name('create');
    Route::post('/', [AnalyseController::class, 'store'])->name('store');
    Route::get('/{analyse}', [AnalyseController::class, 'show'])->name('show');           // protégé par AnalysePolicy::view
    Route::get('/{analyse}/rapport', [AnalyseController::class, 'genererRapport'])->name('rapport'); // <<extend>>
});
/*
|--------------------------------------------------------------------------
| Support — Modération (garant de la fiabilité du Pilier 2)
| 'auth' seul au niveau route : la restriction au rôle Modérateur est faite
| par SignalementPolicy à l'intérieur du contrôleur.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('moderation')->name('moderation.')->group(function () {
    Route::get('/', [ModerationController::class, 'index'])->name('index');
    Route::get('/doublons', [ModerationController::class, 'doublons'])->name('doublons');
    Route::patch('/{signalement}/valider', [ModerationController::class, 'valider'])->name('valider');
    Route::patch('/{signalement}/rejeter', [ModerationController::class, 'rejeter'])->name('rejeter');
});
/*
|--------------------------------------------------------------------------
| Support — Alertes
| Consultation publique / Publication réservée au Modérateur (via AlertePolicy)
|--------------------------------------------------------------------------
*/
Route::prefix('alertes')->name('alertes.')->group(function () {
    Route::get('/', [AlerteController::class, 'index'])->name('index'); // public

    Route::middleware('auth')->group(function () {
        Route::get('/creer', [AlerteController::class, 'create'])->name('create');
        Route::post('/', [AlerteController::class, 'store'])->name('store');
    });
});
/*
|--------------------------------------------------------------------------
| Support — Statistiques publiques
|--------------------------------------------------------------------------
*/
Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index');

/*
|--------------------------------------------------------------------------
| Support — Administration (réservé Administrateur, via UserPolicy)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin/utilisateurs')->name('admin.utilisateurs.')->group(function () {
    Route::get('/', [UtilisateurController::class, 'index'])->name('index');
    Route::patch('/{user}/bloquer', [UtilisateurController::class, 'bloquer'])->name('bloquer');
    Route::patch('/{user}/debloquer', [UtilisateurController::class, 'debloquer'])->name('debloquer');
});