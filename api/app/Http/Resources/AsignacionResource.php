<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsignacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'solicitud_id' => $this->solicitud_id,
            'fecha_asignacion' => $this->fecha_asignacion,
            'comentarios_asignacion' => $this->comentarios_asignacion,
            'estado_asignacion' => $this->estado_asignacion,
            'usuario_asignado' => new UsuarioResource($this->whenLoaded('usuarioAsignado')),
            'asignado_por' => new UsuarioResource($this->whenLoaded('asignadoPor')),
        ];
    }
}
