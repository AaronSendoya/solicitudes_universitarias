<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Public self-registration is only ever for the Estudiante role — creating
 * Administrativo/Tecnico accounts is done by an admin via UsuarioController,
 * so this request has no "rol" field to avoid a privilege-escalation vector.
 */
class RegisterRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
