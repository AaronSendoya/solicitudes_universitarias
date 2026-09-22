<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $estudiante = Rol::where('nombre', Rol::ESTUDIANTE)->firstOrFail();
        $administrativo = Rol::where('nombre', Rol::ADMINISTRATIVO)->firstOrFail();
        $tecnico = Rol::where('nombre', Rol::TECNICO)->firstOrFail();

        foreach ([
            ['nombres' => 'Ana', 'apellidos' => 'Ramírez', 'correo_institucional' => 'estudiante@campusconnect.edu', 'rol_id' => $estudiante->id],
            ['nombres' => 'Carlos', 'apellidos' => 'Gómez', 'correo_institucional' => 'gestor@campusconnect.edu', 'rol_id' => $administrativo->id],
            ['nombres' => 'Luis', 'apellidos' => 'Torres', 'correo_institucional' => 'tecnico@campusconnect.edu', 'rol_id' => $tecnico->id],
        ] as $datos) {
            Usuario::firstOrCreate(
                ['correo_institucional' => $datos['correo_institucional']],
                $datos + ['password' => 'password123']
            );
        }
    }
}
