<?php

namespace App\Http\Requests;

use App\Models\Analyse;
use Illuminate\Foundation\Http\FormRequest;

class SuggestionRedactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! $this->user()) {
            return false;
        }

        // Si l'utilisateur pointe vers une analyse existante, elle doit lui appartenir
        // (empêche de réutiliser gratuitement l'analyse de quelqu'un d'autre)
        if ($this->filled('analyse_id')) {
            return Analyse::where('id', $this->input('analyse_id'))
                ->where('user_id', $this->user()->id)
                ->exists();
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'analyse_id' => ['nullable', 'integer', 'exists:analyses,id'],
            // type/contenu obligatoires uniquement si on n'a PAS d'analyse_id
            // (cf. scénario alternatif 2.1 de la fiche "Signaler une arnaque")
            'type' => ['required_without:analyse_id', 'nullable', 'in:texte,lien,numero,email'],
            'contenu' => ['required_without:analyse_id', 'nullable', 'string'],
        ];
    }
}