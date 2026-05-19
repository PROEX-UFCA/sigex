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

    public function test_usuario_inativo_nao_consegue_fazer_login(): void
    {
        $user = User::factory()->create(['status' => 0, 'password' => bcrypt('senha123')]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha123',
        ])->assertRedirect('/')->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_bloqueia_apos_3_tentativas_falhas(): void
    {
        $user = User::factory()->create(['status' => 1]);

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login.store'), ['email' => $user->email, 'password' => 'senha_errada']);
        }

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'senha_errada'])->assertSessionHas('error');
    }
}
