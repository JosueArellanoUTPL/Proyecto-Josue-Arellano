<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

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

        $response->assertForbidden();
    }
}
