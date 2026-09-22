<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionSolicitud extends Model
{
    protected $table = 'asignaciones_solicitud';

    const CREATED_AT = 'fecha_asignacion';
    const UPDATED_AT = 'updated_at';

    public const ACTIVA = 'Activa';
    public const COMPLETADA = 'Completada';

    protected $fillable = [
        'solicitud_id',
        'usuario_asignado_id',
        'asignado_por_id',
        'comentarios_asignacion',
        'estado_asignacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_asignacion' => 'datetime',
        ];
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function usuarioAsignado(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_asignado_id');
    }

    public function asignadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'asignado_por_id');
    }
}
