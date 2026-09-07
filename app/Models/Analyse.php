<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Analyse extends Model
{
    protected $fillable = ['user_id', 'type', 'date_analyse', 'score_fiabilite', 'conclusion', 'api_appel_effectue'];

    public function utilisateur(): BelongsTo { return $this->belongsTo(User::class); }
    public function signalement(): HasOne { return $this->hasOne(Signalement::class); }
    protected $casts = [
        'date_analyse' => 'datetime',
        'api_appel_effectue' => 'boolean',
    ];
}
