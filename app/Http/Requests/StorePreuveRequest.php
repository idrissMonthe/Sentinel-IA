<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePreuveRequest extends FormRequest
{
    public function authorize(): bool
    {
        
        $signalement = $this->route('signalement');

        return $signalement && $this->user()?->can('create', [\App\Models\Preuve::class, $signalement]);
    }

    public function rules(): array
    {
        return [
            'fichier' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'type' => ['required', 'in:image,document,lien'],
        ];
    }
}