<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $estudiante = Rol::where('nombre', Rol::ESTUDIANTE)->firstOrFail();

        $usuario = Usuario::create([
            ...$request->validated(),
            'rol_id' => $estudiante->id,
        ]);

        $usuario->load('rol');
        $token = $usuario->createToken('mobile')->plainTextToken;

        return response()->json([
            'usuario' => new UsuarioResource($usuario),
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $usuario = Usuario::where('correo_institucional', $request->correo_institucional)->first();

        if (! $usuario || ! Hash::check($request->password, $usuario->password)) {
            throw ValidationException::withMessages([
                'correo_institucional' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $usuario->load('rol');
        $token = $usuario->createToken('mobile')->plainTextToken;

        return response()->json([
            'usuario' => new UsuarioResource($usuario),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(Request $request)
    {
        return new UsuarioResource($request->user()->load('rol'));
    }
}
