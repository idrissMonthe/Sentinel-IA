<?php

namespace App\Http\Controllers;

use App\Models\Statistique;

class StatistiqueController extends Controller
{
    // Consulter les statistiques publiques — accessible au Visiteur, sans authentification,
public function index()
{
    $statistiques = Statistique::latest('date_derniere_mise_a_jour')->first();

    if (! $statistiques || $statistiques->date_derniere_mise_a_jour->lt(now()->subHour())) {
        $statistiques = Statistique::recalculer();
    }

    return view('statistiques.index', compact('statistiques'));
}
}
