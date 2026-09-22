<?php

namespace Database\Seeders;

use App\Models\EstadoSolicitud;
use App\Models\Prioridad;
use App\Models\Recurso;
use App\Models\TipoSolicitud;
use App\Models\Ubicacion;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            EstadoSolicitud::ABIERTA,
            EstadoSolicitud::EN_PROCESO,
            EstadoSolicitud::PENDIENTE_REVISION,
            EstadoSolicitud::CERRADA,
        ] as $nombre) {
            EstadoSolicitud::firstOrCreate(['nombre' => $nombre]);
        }

        foreach (['Mantenimiento', 'Soporte Tecnológico', 'Infraestructura', 'Equipamiento'] as $nombre) {
            TipoSolicitud::firstOrCreate(['nombre' => $nombre]);
        }

        foreach ([
            'Alta' => 3,
            'Media' => 2,
            'Baja' => 1,
        ] as $nombre => $nivel) {
            Prioridad::firstOrCreate(['nombre' => $nombre], ['nivel' => $nivel]);
        }

        foreach ([
            'Pabellón A',
            'Pabellón B',
            'Aula 201',
            'Oficina Administrativa',
            'Laboratorio de Cómputo',
            'Biblioteca',
        ] as $nombre) {
            Ubicacion::firstOrCreate(['nombre' => $nombre]);
        }

        $pabellonA = Ubicacion::where('nombre', 'Pabellón A')->first();
        $laboratorio = Ubicacion::where('nombre', 'Laboratorio de Cómputo')->first();
        $aula201 = Ubicacion::where('nombre', 'Aula 201')->first();

        foreach ([
            ['nombre' => 'Proyector Epson X200', 'tipo' => 'Equipamiento', 'ubicacion_id' => $aula201->id],
            ['nombre' => 'Aire acondicionado Sala Docentes', 'tipo' => 'Infraestructura', 'ubicacion_id' => $pabellonA->id],
            ['nombre' => 'PC Laboratorio 05', 'tipo' => 'Soporte Tecnológico', 'ubicacion_id' => $laboratorio->id],
        ] as $recurso) {
            Recurso::firstOrCreate(['nombre' => $recurso['nombre']], $recurso);
        }
    }
}
