<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // des tentatives échouées (exceptions 5.1 et 5.2 de la fiche S'authentifier)
        // dépend de l'état en base, pas seulement du format des champs — elle reste
        // dans AuthController::login(), une Request ne peut pas la porter proprement.
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }
}