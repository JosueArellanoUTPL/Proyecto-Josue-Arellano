<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo User
 *
 * Representa a los usuarios del sistema (Admin / Técnico).
 * Se usa para autenticación, autorización por rol y registro de acciones
 * como avances, evidencias, etc.
 */
class User extends Authenticatable
{
    /**
     * Traits usados:
     * - HasFactory: permite usar factories para testing
     * - Notifiable: permite notificaciones (emails, etc.)
     */
    use HasFactory, Notifiable;

    /**
     * Atributos que se pueden asignar masivamente (mass assignment).
     * Aquí definimos los campos permitidos en create() / update().
     *
     * IMPORTANTE:
     * - 'role' es clave para el RoleMiddleware (admin / tecnico)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atributos que NO deben mostrarse cuando el modelo
     * se convierte a JSON o array.
     * (seguridad)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts automáticos de atributos.
     *
     * - email_verified_at: se convierte automáticamente a Carbon (datetime)
     * - password: se hashea automáticamente al guardar (Laravel 12)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones futuras (opcional)
    |--------------------------------------------------------------------------
    | Aquí podrían agregarse relaciones como:
    | - avances de indicadores
    | - avances de proyectos
    | si luego se requiere auditoría o trazabilidad
    |
    | Ejemplo:
    | public function avancesIndicadores()
    | {
    |     return $this->hasMany(IndicadorAvance::class);
    | }
    */
}
