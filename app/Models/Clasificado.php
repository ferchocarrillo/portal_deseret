<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clasificado extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Tabla
    |--------------------------------------------------------------------------
    */

    protected $table = 'clasificados';

    /*
    |--------------------------------------------------------------------------
    | Campos asignables
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'user_id',
        'categoria_id',
        'intencion',
        'operacion',
        'titulo',
        'slug',
        'codigo_global',
        'descripcion',
        'precio',
        'pais_id',
        'division_administrativa_id',
        'ciudad_id',
        'rango_cobertura', // <--- Estándar de georreferenciación (local, departamental, nacional)
        'estado',
        'destacado',
        'fecha_publicacion',
        'fecha_cierre',
        'fecha_suspension',
        'motivo_suspension',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'precio'            => 'decimal:2',
        'destacado'         => 'boolean',
        'fecha_publicacion' => 'datetime',
        'fecha_cierre'      => 'datetime',
        'fecha_suspension'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Generador de Código Global (Igual a Marketplace)
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (Clasificado $clasificado) {
            if (empty($clasificado->codigo_global)) {
                $clasificado->codigo_global = self::generarCodigoGlobal();
            }
        });
    }

    public static function generarCodigoGlobal(): string
    {
        $ultimo = self::query()->latest('id')->first();

        if (! $ultimo || empty($ultimo->codigo_global)) {
            $consecutivo = 1;
        } else {
            $numeroActual = (int) preg_replace('/[^0-9]/', '', $ultimo->codigo_global);
            $consecutivo = $numeroActual + 1;
        }

        return sprintf('CLA-%06d', $consecutivo);
    }

    /*
    |--------------------------------------------------------------------------
    | Usuario propietario (Soporte dual: usuario() y user())
    |--------------------------------------------------------------------------
    */

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Categoría
    |--------------------------------------------------------------------------
    */

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(
            ClasificadoCategoria::class,
            'categoria_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Ubicación
    |--------------------------------------------------------------------------
    */

    public function pais(): BelongsTo
    {
        return $this->belongsTo(
            Pais::class,
            'pais_id'
        );
    }

    public function divisionAdministrativa(): BelongsTo
    {
        return $this->belongsTo(
            DivisionAdministrativa::class,
            'division_administrativa_id'
        );
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(
            DivisionAdministrativa::class,
            'division_administrativa_id'
        );
    }

    public function ciudad(): BelongsTo
    {
        return $this->belongsTo(
            Ciudad::class,
            'ciudad_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Información específica
    |--------------------------------------------------------------------------
    */

    public function vehiculo(): HasOne
    {
        return $this->hasOne(
            ClasificadoVehiculo::class,
            'clasificado_id'
        );
    }

    public function propiedad(): HasOne
    {
        return $this->hasOne(
            ClasificadoPropiedad::class,
            'clasificado_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Imágenes
    |--------------------------------------------------------------------------
    */

    public function imagenes(): HasMany
    {
        return $this->hasMany(
            ClasificadoImagen::class,
            'clasificado_id'
        )->orderBy('orden');
    }

    public function imagenPrincipal(): HasOne
    {
        return $this->hasOne(
            ClasificadoImagen::class,
            'clasificado_id'
        )->where('es_principal', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Denuncias
    |--------------------------------------------------------------------------
    */

    public function denuncias(): HasMany
    {
        return $this->hasMany(
            ClasificadoDenuncia::class,
            'clasificado_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePublicados($query)
    {
        return $query->where('estado', 'publicado');
    }

    public function scopeActivos($query)
    {
        return $query->whereIn('estado', [
            'publicado',
            'pausado',
        ]);
    }

    public function scopeOfreciendo($query)
    {
        return $query->where('intencion', 'ofrecer');
    }

    public function scopeBuscando($query)
    {
        return $query->where('intencion', 'buscar');
    }

    public function scopeEnVenta($query)
    {
        return $query->where('operacion', 'venta');
    }

    public function scopeEnArriendo($query)
    {
        return $query->where('operacion', 'arriendo');
    }

    public function scopeDestacados($query)
    {
        return $query
            ->where('destacado', true)
            ->where('estado', 'publicado');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function esDeVehiculo(): bool
    {
        return $this->vehiculo()->exists();
    }

    public function esDePropiedad(): bool
    {
        return $this->propiedad()->exists();
    }

    public function estaPublicado(): bool
    {
        return $this->estado === 'publicado';
    }

    public function esOferta(): bool
    {
        return $this->intencion === 'ofrecer';
    }

    public function esBusqueda(): bool
    {
        return $this->intencion === 'buscar';
    }

    public function esVenta(): bool
    {
        return $this->operacion === 'venta';
    }

    public function esArriendo(): bool
    {
        return $this->operacion === 'arriendo';
    }
}
