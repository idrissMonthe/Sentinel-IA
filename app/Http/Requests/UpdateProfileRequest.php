<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Un utilisateur connecté modifie toujours SON propre profil
        // (pas de paramètre d'id dans la route -> pas de vérification de propriété à faire)
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
        ];
    }
}