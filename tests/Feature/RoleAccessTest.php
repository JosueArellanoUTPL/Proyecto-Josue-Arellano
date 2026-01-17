<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_acceder_a_usuarios()
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/usuarios');

        $response->assertStatus(200);
    }

    public function test_tecnico_no_puede_acceder_a_usuarios()
    {
        $tecnico = User::create([
            'name' => 'Tecnico Test',
            'email' => 'tecnico@test.com',
            'password' => Hash::make('password123'),
            'role' => 'tecnico',
        ]);

        $response = $this->actingAs($tecnico)->get('/usuarios');

        // Tu middleware puede devolver 403 (forbidden) o redirigir.
        // Usamos assertTrue para aceptar cualquiera de las 2 opciones.
        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(),
            'Se esperaba 403 o redirección para el rol técnico.'
        );
    }
}
