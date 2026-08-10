<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Preuve extends Model
{
    use HasFactory;
    protected $fillable = ['signalement_id', 'type', 'fichier'];
      // contient : 1 Signalement -- 1..* Preuve
    public function signalement(): BelongsTo
    {
        return $this->belongsTo(Signalement::class);
    }
}
