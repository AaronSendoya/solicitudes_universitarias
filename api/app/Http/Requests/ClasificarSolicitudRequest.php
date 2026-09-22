<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClasificarSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_id' => ['sometimes', 'required', 'integer', 'exists:tipos_solicitud,id'],
            'prioridad_id' => ['sometimes', 'required', 'integer', 'exists:prioridades,id'],
        ];
    }
}
