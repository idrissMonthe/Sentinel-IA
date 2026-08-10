<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntiteSuspecte extends Model
{
use HasFactory;
protected $fillable = ['type', 'valeur', 'nombre_signalement', 'date_apparition'];
protected function casts(): array
{
    return ['date_apparition' => 'datetime']; 
}
// concerne : 0..1 EntiteSuspecte -- * Signalement
    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class);
    }
}
