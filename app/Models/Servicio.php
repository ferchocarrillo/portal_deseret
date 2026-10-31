<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicios';

    protected $fillable = [
        'user_id',
        'codigo_global',
        'titulo',
        'descripcion',
        'categoria',
        'modalidad',
        'precio_tipo',
        'precio',
        'moneda',
        'imagen_tipo',
        'imagen',
        'pais',
        'departamento',
        'ciudad',
        // NUEVOS CAMPOS GEOESPACIALES
        'mostrar_direccion',
        'latitud',
        'longitud',
        'radio_alcance_km',

        'usar_whatsapp',
        'usar_email',
        'usar_telefono',
        'contacto_whatsapp',
        'contacto_email',
        'contacto_telefono',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'usar_whatsapp' => 'boolean',
            'usar_email' => 'boolean',
            'usar_telefono' => 'boolean',
            'mostrar_direccion' => 'boolean', // Cast a booleano
        ];
    }

    /**
     * Boot del modelo para autogenerar el código global antes de crear.
     */
    protected static function booted(): void
    {
        static::creating(function (Servicio $servicio) {
            if (empty($servicio->codigo_global)) {
                $servicio->codigo_global = self::generarCodigoGlobal();
            }
        });
    }

    /**
     * Genera el consecutivo único con prefijo SER (ej. SER-000001).
     */
    public static function generarCodigoGlobal(): string
    {
        $ultimoServicio = self::query()->latest('id')->first();

        if (! $ultimoServicio || empty($ultimoServicio->codigo_global)) {
            $consecutivo = 1;
        } else {
            $numeroActual = (int) preg_replace('/[^0-9]/', '', $ultimoServicio->codigo_global);
            $consecutivo = $numeroActual + 1;
        }

        return sprintf('SER-%06d', $consecutivo);
    }

    /**
     * Usuario propietario del servicio.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Conversaciones relacionadas con el servicio.
     */
    public function conversaciones(): HasMany
    {
        return $this->hasMany(
            ServicioConversacion::class,
            'servicio_id'
        );
    }

    /**
     * Intentos de contacto registrados para este servicio.
     */
    public function contactos(): HasMany
    {
        return $this->hasMany(ContactoServicio::class);
    }

    /**
     * Todas las reseñas asociadas a este servicio.
     */
    public function resenas(): HasMany
    {
        return $this->hasMany(ResenaServicio::class);
    }

    /**
     * Reseñas que evalúan la atención del proveedor.
     */
    public function resenasAtencion(): HasMany
    {
        return $this->resenas()
            ->where('tipo', 'atencion');
    }

    /**
     * Reseñas que evalúan el servicio recibido.
     */
    public function resenasServicio(): HasMany
    {
        return $this->resenas()
            ->where('tipo', 'servicio');
    }
}
