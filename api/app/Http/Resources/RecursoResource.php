<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecursoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'estado' => $this->estado,
            'ubicacion_id' => $this->ubicacion_id,
            'ubicacion' => $this->whenLoaded('ubicacion', fn () => $this->ubicacion->nombre),
        ];
    }
}
