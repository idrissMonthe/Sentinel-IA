<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cible = $this->route('user');

        return $cible && ($this->user()?->can('updateRole', $cible) ?? false);
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ];
    }
}