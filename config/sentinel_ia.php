<?php

return [
    // Nombre maximal d'analyses IA (donc d'appels facturés) qu'un utilisateur
    // peut déclencher par jour, tous points d'entrée confondus (Analyser un contenu
    // ET Rédiger un signalement assisté par IA consomment le même quota).
    'quota_analyses_jour' => env('sentinel_ia_QUOTA_ANALYSES_JOUR', 5),
];