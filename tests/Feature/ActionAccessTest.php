<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class ActionAccessTest extends TestCase
{
    use RefreshDatabase;
    
    protected bool $seed = true;
    protected string $seeder = \Database\Seeders\DatabaseSeeder::class;


    public function test_usuario_com_permissao_acessa_lista_de_acoes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Desenvolvimento');
        $user->givePermissionTo('ver_todas_as_ações');

        $this->actingAs($user)->get(route('actions.index'))->assertOk();
    }

    public function test_usuario_sem_permissao_nao_acessa_lista_de_acoes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('actions.index'))->assertForbidden();
    }
}
