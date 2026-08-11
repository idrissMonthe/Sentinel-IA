<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSignalementRequest extends FormRequest
{
    public function authorize(): bool
    {
           if (! $this->user()) {
        return false;
    }

    if ($this->filled('analyse_id')) {
        return \App\Models\Analyse::where('id', $this->input('analyse_id'))
            ->where('user_id', $this->user()->id)
            ->exists();
    }

    return true;

    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:numero,email,lien'],
            'valeur' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
            'analyse_id' => ['nullable', 'integer', 'exists:analyses,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required' => 'Merci de décrire l\'arnaque pour que le modérateur puisse l\'évaluer.',
        ];
    }
}