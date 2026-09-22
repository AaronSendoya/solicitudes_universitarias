<?php

namespace App\Models;

use App\Support\CatalogoCache;
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

    /**
     * Resolves the role name from CatalogoCache rather than the 'rol'
     * relation, unless it's already been eager-loaded — roles are almost
     * never changed, so this avoids a ~250ms round trip on every request
     * that checks a permission (i.e. nearly all of them).
     */
    private function nombreRol(): ?string
    {
        return $this->relationLoaded('rol') ? $this->rol?->nombre : CatalogoCache::rolNombre($this->rol_id);
    }

    public function esEstudiante(): bool
    {
        return $this->nombreRol() === Rol::ESTUDIANTE;
    }

    public function esAdministrativo(): bool
    {
        return $this->nombreRol() === Rol::ADMINISTRATIVO;
    }

    public function esTecnico(): bool
    {
        return $this->nombreRol() === Rol::TECNICO;
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
