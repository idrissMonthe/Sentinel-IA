<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'confirmed', Password::min(8)],
        ];
    }
    public function messages(): array
    {
        return [
            'password' => 'Le mot de passe doit contenir à la fois des lettres et des chiffres.',
        ];
    }
}