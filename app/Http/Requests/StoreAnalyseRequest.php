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
            'contenu' => ['required_without:fichier', 'nullable', 'string'],
            'fichier' => ['required_without:contenu', 'nullable', 'file', 'image', 'max:5120'],
        ];
    }
}