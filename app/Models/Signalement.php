<?php

namespace App\Models;

use App\Enums\StatutSignalement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'moderateur_id', 'entite_suspecte_id',
        'description', 'ville', 'statut',
    ];

    protected function casts(): array
    {
        return ['statut' => StatutSignalement::class];
    }

    // declare : * Signalement -- 1 Utilisateur
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // traite : * Signalement -- 0..1 Moderateur
    public function moderateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderateur_id');
    }

    // concerne : * Signalement -- 0..1 EntiteSuspecte
    public function entiteSuspecte(): BelongsTo
    {
        return $this->belongsTo(EntiteSuspecte::class);
    }

    // contient : 1 Signalement -- 1..* Preuve
    public function preuves(): HasMany
    {
        return $this->hasMany(Preuve::class);
    }
}