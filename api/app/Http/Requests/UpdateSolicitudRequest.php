<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:255'],
            'descripcion' => ['sometimes', 'required', 'string'],
            'tipo_id' => ['sometimes', 'required', 'integer', 'exists:tipos_solicitud,id'],
            'ubicacion_id' => ['sometimes', 'required', 'integer', 'exists:ubicaciones,id'],
            'recurso_id' => ['nullable', 'integer', 'exists:recursos,id'],
        ];
    }
}
