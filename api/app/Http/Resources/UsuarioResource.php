<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'nombre_completo' => trim("{$this->nombres} {$this->apellidos}"),
            'correo_institucional' => $this->correo_institucional,
            'rol' => $this->whenLoaded('rol', fn () => $this->rol->nombre),
            'rol_id' => $this->rol_id,
            'fecha_registro' => $this->fecha_registro,
        ];
    }
}
