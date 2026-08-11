<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Analyse extends Model
{
    protected $fillable = ['user_id', 'date_analyse', 'score_fiabilite', 'conclusion'];

    public function utilisateur(): BelongsTo { return $this->belongsTo(User::class); }
    public function signalement(): HasOne { return $this->hasOne(Signalement::class); }
}
