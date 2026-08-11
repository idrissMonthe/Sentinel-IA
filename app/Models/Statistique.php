<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistique extends Model
{
    public $timestamps = false;
    protected $fillable = ['date_derniere_mise_a_jour', 'total_signalement_actifs', 'total_entites_bannies'];

    public static function recalculer(): self
{
    return self::create([
        'date_derniere_mise_a_jour' => now(),
        'total_signalement_actifs' => \App\Models\Signalement::where('statut', 'valide')->count(),
        'total_entites_bannies' => \App\Models\EntiteSuspecte::where('nombre_signalement', '>=', 5)->count(),
    ]);
}
}
