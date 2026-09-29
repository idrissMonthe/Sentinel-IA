<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalyseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function messages(): array
    {
        return [
            'fichier.required_if' => 'Veuillez choisir une capture d’écran à analyser.',
            'fichier.image' => 'Le fichier doit être une image valide.',
            'fichier.mimes' => 'Formats acceptés : JPG, JPEG ou PNG.',
            'fichier.max' => 'L’image ne doit pas dépasser 5 Mo.',
            'contenu.required_unless' => 'Veuillez saisir le contenu à analyser.',
        ];
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:texte,lien,numero,email,image'],
            // Une image doit toujours provenir d'un upload validé, jamais d'un chemin fourni par le client.
            'contenu' => ['exclude_if:type,image', 'required_unless:type,image', 'nullable', 'string', 'max:12000'],
            'fichier' => ['exclude_unless:type,image', 'required_if:type,image', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}
