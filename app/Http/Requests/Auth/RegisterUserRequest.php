<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Créer un compte est un cas d'utilisation du Visiteur : accessible à tous
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'telephone' => preg_replace('/\s+/', '', (string) $this->input('telephone')) ?: null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'string', 'email:rfc', 'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/i', 'max:255', Rule::unique('users', 'email')],
            'telephone' => ['nullable', 'string', 'regex:/^6[0-9]{8}$/'],
            'password' => ['required', 'confirmed', Password::min(8),]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Votre adresse email est obligatoire.',
            'email.email' => 'Saisissez une adresse email valide, par exemple example@test.com.',
            'email.regex' => 'Saisissez une adresse email complète, par exemple example@test.com.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'telephone.regex' => 'Le téléphone doit commencer par 6 et contenir exactement 9 chiffres.',
        ];
    }
    protected function failedValidation(Validator $validator): void
     {
        throw (new ValidationException($validator))
            ->redirectTo(route('register'));
    }
}
