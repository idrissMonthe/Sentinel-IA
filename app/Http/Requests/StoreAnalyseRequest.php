<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnalyseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:texte,lien,numero,email,image'],
            // Une image doit toujours provenir d'un upload validé, jamais d'un chemin fourni par le client.
            'contenu' => ['required_unless:type,image', 'prohibited_if:type,image', 'nullable', 'string', 'max:12000'],
            'fichier' => ['required_if:type,image', 'nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }
}
