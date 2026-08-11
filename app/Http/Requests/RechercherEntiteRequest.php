<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechercherEntiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Rechercher est accessible au Visiteur sans compte : rien à vérifier
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['nullable', 'in:numero,email,lien'],
            'valeur' => ['nullable', 'string', 'max:255'],
        ];
    }
}