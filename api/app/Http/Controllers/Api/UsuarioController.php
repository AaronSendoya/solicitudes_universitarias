<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        if ($request->filled('rol')) {
            $query->whereHas('rol', fn ($q) => $q->where('nombre', $request->string('rol')));
        }

        return UsuarioResource::collection($query->orderBy('nombres')->get());
    }

    public function store(StoreUsuarioRequest $request)
    {
        $rol = Rol::where('nombre', $request->rol)->firstOrFail();

        $usuario = Usuario::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'correo_institucional' => $request->correo_institucional,
            'password' => $request->password,
            'rol_id' => $rol->id,
        ]);

        return new UsuarioResource($usuario->load('rol'));
    }
}
