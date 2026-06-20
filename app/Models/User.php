<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Roles reales que estoy usando en el proyecto.
    public const ROLE_ADMIN = 'admin';
    public const ROLE_PLANIFICACION = 'planificacion';
    public const ROLE_TECNICO = 'tecnico';
    public const ROLE_CONSULTA = 'consulta';

    // Etiquetas bonitas para mostrar el rol en las vistas.
    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'Administrador del Sistema',
        self::ROLE_PLANIFICACION => 'Responsable de Planificacion',
        self::ROLE_TECNICO => 'Tecnico de Seguimiento',
        self::ROLE_CONSULTA => 'Autoridad / Consulta',
    ];

    // Campos que se pueden guardar desde formularios.
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
    ];

    // Datos ocultos por seguridad cuando el usuario se convierte a array o JSON.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public static function roleKeys(): array
    {
        // Devuelve solo las claves: admin, planificacion, tecnico, consulta.
        return array_keys(self::ROLE_LABELS);
    }

    public function roleLabel(): string
    {
        // Convierte el rol guardado en base a una etiqueta entendible.
        return self::ROLE_LABELS[$this->role] ?? 'Sin rol asignado';
    }

    public function isAdmin(): bool
    {
        // Atajo para saber si el usuario es administrador.
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTecnicoSeguimiento(): bool
    {
        // Atajo para saber si puede trabajar como tecnico de seguimiento.
        return $this->role === self::ROLE_TECNICO;
    }

    public function canManagePlanning(): bool
    {
        // Permiso usado en menu y rutas de planificacion.
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_PLANIFICACION,
        ], true);
    }

    public function canRegisterSeguimiento(): bool
    {
        // Permiso usado para mostrar botones de registrar avances.
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_TECNICO,
        ], true);
    }
}
