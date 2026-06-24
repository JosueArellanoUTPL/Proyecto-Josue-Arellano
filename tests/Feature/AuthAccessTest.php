<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_acceso_sin_login_redirige_a_login()
    {
        // Rutas protegidas por auth (elige una que tengas segura)
        $response = $this->get('/planes');

        // En Breeze normalmente redirige a /login
        $response->assertRedirect('/login');
    }
}
