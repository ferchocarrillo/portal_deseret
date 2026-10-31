<?php

namespace App\Models;

use Database\Factories\UserFactory;
use App\Models\UserInfraction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public const ROL_ADMINISTRADOR = 'Administrador';
    public const ROL_USUARIO = 'Usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'foto',
        'fecha_nacimiento',
        'telefono',
        'ultima_visita',
        'ultima_conexion',
        'numero_accesos',
        'debe_cambiar_password',
        'activo',             // <-- OBLIGATORIO AQUÍ
        'suspendido_at',
        'bloqueado_hasta',    // <-- OBLIGATORIO AQUÍ
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'     => 'datetime',
            'password'              => 'hashed',
            'fecha_nacimiento'      => 'date',
            'ultima_visita'         => 'datetime',
            'ultima_conexion'       => 'datetime',
            'numero_accesos'        => 'integer',
            'debe_cambiar_password' => 'boolean',
            'activo'                => 'boolean',
            'suspendido_at'         => 'datetime',
            'bloqueado_hasta'       => 'datetime',
        ];
    }

    public function publicaciones(): HasMany
    {
        return $this->hasMany(Publicacion::class, 'user_id');
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class, 'user_id');
    }

    public function reacciones(): HasMany
    {
        return $this->hasMany(Reaccion::class, 'user_id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'user_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'user_id');
    }

    public function calificacionesRecibidas(): MorphMany
    {
        return $this->morphMany(Calificacion::class, 'calificable');
    }

    public function interacciones(): HasMany
    {
        return $this->hasMany(Interaccion::class, 'user_id');
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'user_id');
    }

    public function invitaciones(): HasMany
    {
        return $this->hasMany(Invitacion::class, 'user_id');
    }

    public function invitacionesRecibidas(): HasMany
    {
        return $this->hasMany(Invitacion::class, 'usuario_invitado_id');
    }

    public function formaciones(): HasMany
    {
        return $this->hasMany(UserFormacion::class, 'user_id');
    }

    public function experiencias(): HasMany
    {
        return $this->hasMany(UserExperiencia::class, 'user_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'user_skills',
            'user_id',
            'skill_id'
        )->withTimestamps();
    }

    public function userSkills(): HasMany
    {
        return $this->hasMany(UserSkill::class, 'user_id');
    }

    public function hojasVida(): HasMany
    {
        return $this->hasMany(UserHojaVida::class, 'user_id');
    }

    public function hojaVidaPrincipal(): HasOne
    {
        return $this->hasOne(UserHojaVida::class, 'user_id')->where('principal', true);
    }

    public function empresaUsuarios(): HasMany
    {
        return $this->hasMany(EmpresaUsuario::class, 'user_id');
    }

    public function ofertasEmpleo(): HasMany
    {
        return $this->hasMany(OfertaEmpleo::class, 'user_id');
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(Postulacion::class, 'user_id');
    }

    public function esAdministrador(): bool
    {
        return $this->role === self::ROL_ADMINISTRADOR;
    }

    public function esUsuario(): bool
    {
        return $this->role === self::ROL_USUARIO;
    }

    public function informacionRecepcion(): HasOne
    {
        return $this->hasOne(UserInformacionRecepcion::class, 'user_id');
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'user_id');
    }

    public function infractions(): HasMany
    {
        return $this->hasMany(UserInfraction::class);
    }

    public function moderatedInfractions(): HasMany
    {
        return $this->hasMany(UserInfraction::class, 'moderator_id');
    }
}
