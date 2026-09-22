<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado_id' => ['required', 'integer', 'exists:estados_solicitud,id'],
            'comentario' => ['nullable', 'string'],
        ];
    }
}
