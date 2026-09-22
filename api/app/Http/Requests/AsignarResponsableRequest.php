<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignarResponsableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario_asignado_id' => ['required', 'integer', 'exists:usuarios,id'],
            'comentarios_asignacion' => ['nullable', 'string'],
        ];
    }
}
