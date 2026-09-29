<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsommationIA extends Model
{
    protected $table = 'consommations_ia';

    protected $fillable = ['operation', 'modele', 'request_id', 'input_tokens', 'output_tokens', 'cache_creation_input_tokens', 'cache_read_input_tokens'];
}
