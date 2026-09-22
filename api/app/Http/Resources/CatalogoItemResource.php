<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Generic {id, nombre} resource shared by the lookup-table endpoints
 * (estados, tipos, prioridades, ubicaciones).
 */
class CatalogoItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return array_filter([
            'id' => $this->id,
            'nombre' => $this->nombre,
            'nivel' => $this->nivel ?? null,
        ], fn ($value) => ! is_null($value));
    }
}
