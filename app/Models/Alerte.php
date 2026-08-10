<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerte extends Model
{
    use HasFactory;

    protected $fillable = ['moderateur_id', 'titre', 'contenu', 'est_publiee'];

    protected function casts(): array
    {
        return ['est_publiee' => 'boolean'];
    }

    // Publie : 0..1 Moderateur -- * Alerte
    public function moderateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderateur_id');
    }
}