<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComentarioRequest;
use App\Http\Resources\ComentarioResource;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function index(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return ComentarioResource::collection(
            $solicitud->comentarios()->with('autor')->get()
        );
    }

    public function store(StoreComentarioRequest $request, Solicitud $solicitud)
    {
        $this->authorize('comentar', $solicitud);

        $comentario = $solicitud->comentarios()->create([
            'usuario_autor_id' => $request->user()->id,
            'texto_comentario' => $request->texto_comentario,
        ]);

        return new ComentarioResource($comentario->load('autor'));
    }
}
