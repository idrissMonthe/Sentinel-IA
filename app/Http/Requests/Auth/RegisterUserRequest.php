<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Créer un compte est un cas d'utilisation du Visiteur : accessible à tous
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8),]
        ];
    }
    protected function failedValidation(Validator $validator): void
     {
        throw (new ValidationException($validator))
            ->redirectTo(route('register'));
    }
}