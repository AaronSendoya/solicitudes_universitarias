<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogoItemResource;
use App\Http\Resources\RecursoResource;
use App\Models\EstadoSolicitud;
use App\Models\Prioridad;
use App\Models\Recurso;
use App\Models\Rol;
use App\Models\TipoSolicitud;
use App\Models\Ubicacion;

class CatalogoController extends Controller
{
    public function roles()
    {
        return CatalogoItemResource::collection(Rol::orderBy('nombre')->get());
    }

    public function estados()
    {
        return CatalogoItemResource::collection(EstadoSolicitud::orderBy('id')->get());
    }

    public function tipos()
    {
        return CatalogoItemResource::collection(TipoSolicitud::orderBy('nombre')->get());
    }

    public function prioridades()
    {
        return CatalogoItemResource::collection(Prioridad::orderByDesc('nivel')->get());
    }

    public function ubicaciones()
    {
        return CatalogoItemResource::collection(Ubicacion::orderBy('nombre')->get());
    }

    public function recursos()
    {
        return RecursoResource::collection(Recurso::with('ubicacion')->orderBy('nombre')->get());
    }
}
