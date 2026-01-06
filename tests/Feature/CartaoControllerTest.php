<?php

namespace Tests\Feature;

use App\Models\Cartao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartaoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_comum_nao_pode_cadastrar_cartao_para_outro_user()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $user = User::factory()->create();
        $cartao = ['user_id' => $user->id, 'number'=>'1234567890123456',
            'data_validade' => '02/28',
            'cvv' => 123,
            'saldo' => 5690.01,
            'status' => 'ATIVO'];

        $response = $this->actingAs($userLogado)->post('api/cartoes', $cartao);

        $response->assertJsonFragment([
            'user_id' => $userLogado->id,
        ]);
    }

    public function test_user_comum_nao_ve_cartoes_de_outros_users()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $cartaoUserLogado = Cartao::factory()->create(['user_id' => $userLogado->id]);
        $outroUser = User::factory()->create();
        $cartaoOutroUser = Cartao::factory()->create(['user_id' => $outroUser->id]);

        $response = $this->actingAs($userLogado)->getJson('api/cartoes');

        $response->assertJsonFragment([
            'id' => $cartaoUserLogado->id
        ]);

        $response->assertJsonMissing([
            'id' => $cartaoOutroUser->id
        ]);
        $response->assertStatus(200);
    }

    public function test_user_comum_nao_pode_editar_cartao()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $cartaoUserLogado = Cartao::factory()->create([
            'user_id' => $userLogado->id,
        ]);
        $dadosAtualizados = [
            'number' => '1234567890123456',
            'data_validade' => '02/28',
            'cvv' => '123',
            'saldo' => 5690.01,
            'user_id' => $userLogado->id,
            'status' => 'ATIVO'
        ];
        $response = $this->actingAs($userLogado)->putJson('api/cartoes/'.$cartaoUserLogado->id,
            $dadosAtualizados);
        $response->assertStatus(403);
    }

    public function test_admin_pode_editar_cartao()
    {
        $userLogado = User::factory()->create([
            'is_admin' => true,
        ]);
        $cartaoUserLogado = Cartao::factory()->create([
            'user_id' => $userLogado->id,
        ]);

        $dadosAtualizados = [
            'number' => '1234567890123456',
            'data_validade' => '02/28',
            'cvv' => '123',
            'saldo' => 5690.01,
            'user_id' => $userLogado->id,
            'status' => 'ATIVO'
        ];
        $response = $this->actingAs($userLogado)->putJson('api/cartoes/'.$cartaoUserLogado->id,
        $dadosAtualizados);
        //dd($response->getContent());
        $response->assertStatus(200);
    }

    public function test_user_comum_nao_deleta_cartao_de_outro_user()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $cartaoUserLogado = Cartao::factory()->create([
            'user_id' => $userLogado->id,
        ]);
        $user = User::factory()->create();
        $cartao = Cartao::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($userLogado)->deleteJson('api/cartoes/'.$cartao->id);
        $response->assertStatus(403);
    }
}
