<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = 'fecha_actualizacion';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_cierre',
        'estado_id',
        'tipo_id',
        'prioridad_id',
        'ubicacion_id',
        'recurso_id',
        'usuario_solicitante_id',
        'usuario_responsable_actual_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_creacion' => 'datetime',
            'fecha_actualizacion' => 'datetime',
            'fecha_cierre' => 'datetime',
        ];
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoSolicitud::class, 'tipo_id');
    }

    public function prioridad(): BelongsTo
    {
        return $this->belongsTo(Prioridad::class, 'prioridad_id');
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function recurso(): BelongsTo
    {
        return $this->belongsTo(Recurso::class, 'recurso_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_solicitante_id');
    }

    public function responsableActual(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_responsable_actual_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionSolicitud::class);
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(ComentarioSolicitud::class)->orderBy('fecha_comentario');
    }

    public function archivosAdjuntos(): HasMany
    {
        return $this->hasMany(ArchivoAdjunto::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(HistorialSolicitud::class)->orderByDesc('fecha_cambio');
    }
}
