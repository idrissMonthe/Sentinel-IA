<?php

namespace App\Http\Controllers;

use App\Models\Statistique;

class StatistiqueController extends Controller
{
    // Consulter les statistiques publiques — accessible au Visiteur, sans authentification,
    // cohérent avec la réorientation du diagramme de cas d'utilisation
    public function index()
    {
        // Lecture du cache uniquement : jamais de recalcul à la volée ici
        // (le calcul lourd — calculerTauxResolution(), listerArnaquesFrequentes() —
        // est déclenché par une tâche planifiée, pas par une requête utilisateur)
        $statistiques = Statistique::latest('date_derniere_mise_a_jour')->first();

        return view('statistiques.index', compact('statistiques'));
    }
}