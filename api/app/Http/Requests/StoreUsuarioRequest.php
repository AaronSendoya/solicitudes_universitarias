<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Used by an Administrativo to create staff accounts (Administrativo/Tecnico).
 */
class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'correo_institucional' => ['required', 'email', 'max:255', 'unique:usuarios,correo_institucional'],
            'password' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'string', Rule::in(['Estudiante', 'Administrativo', 'Tecnico'])],
        ];
    }
}
