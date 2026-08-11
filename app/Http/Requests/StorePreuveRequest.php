<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePreuveRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Déplacé ici depuis le abort_unless() du contrôleur : vérifier le droit d'agir
        // sur la ressource ciblée par la route est la responsabilité naturelle d'une Request.
        $signalement = $this->route('signalement');

        return $signalement && $signalement->user_id === $this->user()?->id;
    }

    public function rules(): array
    {
        return [
            'fichier' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'type' => ['required', 'in:image,document,lien'],
        ];
    }
}