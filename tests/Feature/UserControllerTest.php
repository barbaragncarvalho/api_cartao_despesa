<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastrar_user_admin()
    {
        $userLogado = User::factory()->create();
        $dadosUser = [
            'name' => 'Ana',
            'email' => "ana@gmail.com",
            'password' => bcrypt('123456'),
            'is_admin' => true
        ];
        $response = $this->actingAs($userLogado)->postJson('api/users', $dadosUser);
        $response->assertStatus(201);
    }

    public function test_cadastrar_user_comum(){
        $userLogado = User::factory()->create();
        $dadosUser = [
            'name' => 'Ana',
            'email' => "ana@gmail.com",
            'password' => bcrypt('123456'),
            'is_admin' => false
        ];
        $response = $this->actingAs($userLogado)->postJson('api/users', $dadosUser);
        $response->assertStatus(201);
    }

    public function test_user_comum_nao_consegue_ver_outros_users()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false
        ]);
        $response = $this->actingAs($userLogado)->getJson('api/users');
        $response->assertStatus(403);
    }

    public function test_admin_consegue_ver_todos_users()
    {
        $userLogado = User::factory()->create([
            'is_admin' => true
        ]);
        $response = $this->actingAs($userLogado)->getJson('api/users');
        $response->assertStatus(200);
    }

    public function test_user_comum_consegue_ver_seus_dados_somente()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false
        ]);
        $response = $this->actingAs($userLogado)->getJson('api/users/'.$userLogado->id);
        $response->assertStatus(200);
    }

    public function test_admin_consegue_ver_dados_de_todos_users()
    {
        $userLogado = User::factory()->create([
            'is_admin' => true
        ]);
        $response = $this->actingAs($userLogado)->getJson('api/users');
        $response->assertStatus(200);
    }

    public function test_user_comum_nao_consegue_atualizar_outro_user()
    {
        $userLogado = User::factory()->create();
        $outroUser = User::factory()->create();
        $response = $this->actingAs($userLogado)->putJson('api/users/'.$outroUser->id);
        $response->assertStatus(403);
    }

    public function test_admin_consegue_atualizar_outro_user()
    {
        $userLogado = User::factory()->create([
            'is_admin' => true
        ]);
        $outroUser = User::factory()->create();
        $response = $this->actingAs($userLogado)->putJson('api/users/'.$outroUser->id);
        $response->assertStatus(200);
    }

    public function test_user_comum_nao_pode_se_promover_a_admin()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false
        ]);
        $response = $this->actingAs($userLogado)->putJson('api/users/'.$userLogado->id, ['is_admin' => true]);
        $response->assertJsonFragment(['is_admin' => false]);
    }

    public function test_user_comum_nao_pode_remover_outro_user()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false
        ]);
        $outroUser = User::factory()->create();
        $response = $this->actingAs($userLogado)->deleteJson('api/users/'.$outroUser->id);
        $response->assertStatus(403);
    }

    public function test_admin_consegue_remover_outro_user()
    {
        $userLogado = User::factory()->create([
            'is_admin' => true
        ]);
        $outroUser = User::factory()->create();
        $response = $this->actingAs($userLogado)->deleteJson('api/users/'.$outroUser->id);
        $response->assertStatus(204);
    }

    public function test_login_com_credenciais_invalidas()
    {
        $user = User::factory()->create([
            'name' => 'Arthur',
            'email' => 'arthur@gmail.com',
            'password' => bcrypt('123456')
        ]);
        $response = $this->actingAsGuest()->postJson('api/login', [
            'email' => 'arthur@gmail.com',
            'password' => '111112'
        ]);
        $response->assertStatus(401);
    }
}
