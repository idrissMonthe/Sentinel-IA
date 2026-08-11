<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSignalementRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La route est déjà protégée par le middleware 'auth' ;
        // ce champ garde la Request explicite sur ses préconditions
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:numero,email,lien'],
            'valeur' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Merci de décrire l\'arnaque pour que le modérateur puisse l\'évaluer.',
        ];
    }
}