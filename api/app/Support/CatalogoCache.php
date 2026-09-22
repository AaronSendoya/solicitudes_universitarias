<?php

namespace App\Support;

use App\Models\EstadoSolicitud;
use App\Models\Prioridad;
use App\Models\Recurso;
use App\Models\Rol;
use App\Models\TipoSolicitud;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\Cache;

/**
 * In-memory-cached lookups for the small, rarely-changing catalog tables.
 *
 * The detail/listing endpoints used to eager-load each of these as a
 * separate Eloquent relation, which meant a dedicated DB round trip per
 * field. Against a remote database each round trip costs ~200-300ms, so a
 * single solicitud with 5 catalog fields paid ~1.5s just for lookups that
 * rarely, if ever, change. Caching them removes that cost entirely.
 */
class CatalogoCache
{
    private const TTL = 3600;

    public static function estadoNombre(?int $id): ?string
    {
        return $id ? (self::estados()[$id] ?? null) : null;
    }

    public static function tipoNombre(?int $id): ?string
    {
        return $id ? (self::tipos()[$id] ?? null) : null;
    }

    public static function prioridadNombre(?int $id): ?string
    {
        return $id ? (self::prioridades()[$id] ?? null) : null;
    }

    public static function ubicacionNombre(?int $id): ?string
    {
        return $id ? (self::ubicaciones()[$id] ?? null) : null;
    }

    public static function recursoNombre(?int $id): ?string
    {
        return $id ? (self::recursos()[$id] ?? null) : null;
    }

    public static function rolNombre(?int $id): ?string
    {
        return $id ? (self::roles()[$id] ?? null) : null;
    }

    private static function estados(): array
    {
        return Cache::remember('catalogo.estados', self::TTL, fn () => EstadoSolicitud::pluck('nombre', 'id')->all());
    }

    private static function tipos(): array
    {
        return Cache::remember('catalogo.tipos', self::TTL, fn () => TipoSolicitud::pluck('nombre', 'id')->all());
    }

    private static function prioridades(): array
    {
        return Cache::remember('catalogo.prioridades', self::TTL, fn () => Prioridad::pluck('nombre', 'id')->all());
    }

    private static function ubicaciones(): array
    {
        return Cache::remember('catalogo.ubicaciones', self::TTL, fn () => Ubicacion::pluck('nombre', 'id')->all());
    }

    private static function recursos(): array
    {
        return Cache::remember('catalogo.recursos', self::TTL, fn () => Recurso::pluck('nombre', 'id')->all());
    }

    private static function roles(): array
    {
        return Cache::remember('catalogo.roles', self::TTL, fn () => Rol::pluck('nombre', 'id')->all());
    }

    /**
     * Forget every cached catalog. Call this after any change is made to a
     * catalog table (there is currently no endpoint that does so, but this
     * keeps the cache honest if one is added later, e.g. via `php artisan
     * tinker` or a future admin screen).
     */
    public static function flush(): void
    {
        foreach (['estados', 'tipos', 'prioridades', 'ubicaciones', 'recursos', 'roles'] as $key) {
            Cache::forget("catalogo.{$key}");
        }
    }
}
