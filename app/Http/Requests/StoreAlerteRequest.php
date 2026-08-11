<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlerteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Publier une alerte : réservé au Modérateur 
        return $this->user()?->can('create', \App\Models\Alerte::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'action' => ['required', 'in:publier,brouillon'],
        ];
    }
}