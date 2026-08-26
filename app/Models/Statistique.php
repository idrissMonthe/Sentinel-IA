<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistique extends Model
{
    public $timestamps = false;
      protected $fillable = [
        'date_derniere_mise_a_jour',
        'total_signalement_actifs',
        'total_entites_bannies',
        'analyses_effectuees',
        'utilisateurs_proteges',
        'menaces_frequentes',
        'zones_touchees',
    ];

     protected function casts(): array
    {
        return [
            'date_derniere_mise_a_jour' => 'datetime',
            'menaces_frequentes' => 'array',
            'zones_touchees' => 'array',
        ];
    }

       public static function recalculer(): self
    {
        $topMenaces = \App\Models\EntiteSuspecte::select('type')
            ->selectRaw('count(*) as total')
            ->groupBy('type')->orderByDesc('total')->limit(5)->get()
            ->map(fn ($l) => ['label' => ucfirst($l->type), 'total' => $l->total]);

        $topZones = \App\Models\Signalement::whereNotNull('ville')
            ->select('ville')->selectRaw('count(*) as total')
            ->groupBy('ville')->orderByDesc('total')->limit(5)->get()
            ->map(fn ($l) => ['label' => $l->ville, 'total' => $l->total]);

        return self::create([
            'date_derniere_mise_a_jour' => now(),
            'total_signalement_actifs' => \App\Models\Signalement::where('statut', 'valide')->count(),
            'total_entites_bannies' => \App\Models\EntiteSuspecte::where('nombre_signalement', '>=', 5)->count(),
            'analyses_effectuees' => \App\Models\Analyse::count(),
            'utilisateurs_proteges' => \App\Models\User::whereHas('signalements')->orWhereHas('analyses')->count(),
            'menaces_frequentes' => $topMenaces,
            'zones_touchees' => $topZones,
        ]);
    }

}
