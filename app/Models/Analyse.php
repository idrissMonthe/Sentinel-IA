<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Analyse extends Model
{
    protected $fillable = ['user_id', 'type', 'date_analyse', 'score_fiabilite', 'conclusion', 'api_appel_effectue', 'source_web'];

    public function utilisateur(): belongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signalement(): HasOne
    {
        return $this->hasOne(Signalement::class);
    }

    protected $casts = [
        'source_web' => 'array',
        'date_analyse' => 'datetime',
        'api_appel_effectue' => 'boolean',
    ];
}
