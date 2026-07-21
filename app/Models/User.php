<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Roles del sistema.
    public const ROLE_ADMIN = 'admin';

    public const ROLE_PLANIFICACION = 'planificacion';

    public const ROLE_TECNICO = 'tecnico';

    public const ROLE_CONSULTA = 'consulta';

    public const ROLE_APROBADOR = 'aprobador';

    public const ROLE_LABELS = [
        self::ROLE_ADMIN => 'Administrador del Sistema',
        self::ROLE_PLANIFICACION => 'Responsable de Planificacion',
        self::ROLE_TECNICO => 'Tecnico de Seguimiento',
        self::ROLE_CONSULTA => 'Autoridad / Consulta',
        self::ROLE_APROBADOR => 'Aprobador POA',
    ];

    // Campos permitidos.
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo',
    ];

    // Campos ocultos.
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversion de tipos.
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // Lista de roles disponibles.
    public static function roleKeys(): array
    {
        return array_keys(self::ROLE_LABELS);
    }

    // Nombre visible del rol.
    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? 'Sin rol asignado';
    }

    // Validacion de administrador.
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Validacion de acceso a planificacion.
    public function canManagePlanning(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_PLANIFICACION,
        ], true);
    }

    // Validacion de acceso a seguimiento.
    public function canRegisterSeguimiento(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMIN,
            self::ROLE_TECNICO,
        ], true);
    }

    public function isAprobador(): bool
    {
        return $this->role === self::ROLE_APROBADOR;
    }
}
