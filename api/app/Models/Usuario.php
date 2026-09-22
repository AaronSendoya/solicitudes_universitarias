<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'nombres',
        'apellidos',
        'correo_institucional',
        'password',
        'rol_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'fecha_registro' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function esEstudiante(): bool
    {
        return $this->rol?->nombre === Rol::ESTUDIANTE;
    }

    public function esAdministrativo(): bool
    {
        return $this->rol?->nombre === Rol::ADMINISTRATIVO;
    }

    public function esTecnico(): bool
    {
        return $this->rol?->nombre === Rol::TECNICO;
    }

    public function solicitudesCreadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'usuario_solicitante_id');
    }

    public function solicitudesAsignadas(): HasMany
    {
        return $this->hasMany(Solicitud::class, 'usuario_responsable_actual_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionSolicitud::class, 'usuario_asignado_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(ComentarioSolicitud::class, 'usuario_autor_id');
    }

    public function archivosSubidos(): HasMany
    {
        return $this->hasMany(ArchivoAdjunto::class, 'usuario_subidor_id');
    }
}
