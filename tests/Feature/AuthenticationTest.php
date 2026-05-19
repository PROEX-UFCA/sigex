<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

use App\Models\User;

class AuthenticationTest extends TestCase
{

    use RefreshDatabase;

    public function test_usuario_ativo_consegue_fazer_login(): void
    {
        $user = User::factory()->create(['status' => 1, 'password' => bcrypt('senha123')]);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha123',
        ]);

        $response->assertRedirect(route('home.index'));
        $this->assertAuthenticatedAs($user);
    }

    
}
