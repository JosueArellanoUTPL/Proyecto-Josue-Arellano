<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_acceso_sin_login_redirige_a_login()
    {
        // Rutas protegidas por auth (elige una que tengas segura)
        $response = $this->get('/plans');

        // En Breeze normalmente redirige a /login
        $response->assertRedirect('/login');
    }
}
