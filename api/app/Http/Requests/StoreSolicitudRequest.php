<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'tipo_id' => ['required', 'integer', 'exists:tipos_solicitud,id'],
            'ubicacion_id' => ['required', 'integer', 'exists:ubicaciones,id'],
            'recurso_id' => ['nullable', 'integer', 'exists:recursos,id'],
            'prioridad_id' => ['nullable', 'integer', 'exists:prioridades,id'],
        ];
    }
}
