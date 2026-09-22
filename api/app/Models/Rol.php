<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rol extends Model
{
    public const ESTUDIANTE = 'Estudiante';
    public const ADMINISTRATIVO = 'Administrativo';
    public const TECNICO = 'Tecnico';

    protected $table = 'roles';

    protected $fillable = ['nombre'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }
}
