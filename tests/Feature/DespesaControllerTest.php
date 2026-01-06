<?php

namespace Feature;

use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DespesaControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_cadastrar_despesa_com_saldo_insuficiente_no_cartao()
    {
        $user = User::factory()->create();
        $cartao = Cartao::factory()->create([
            'user_id' => $user->id,
            'saldo' => 0
        ]);
        $dadosDespesa = ['descricao' => 'Despesa', 'valor' => 50, 'cartao_id' => $cartao->id];
        $response = $this->actingAs($user)->postJson('api/despesas', $dadosDespesa);
        $response->assertStatus(402)->assertJsonFragment([
            'message' => 'Você não tem saldo suficente para realizar esta transação.',
        ]);
    }

    public function test_cadastrar_despesa_com_saldo_suficiente_no_cartao()
    {
        Mail::fake();
        $user = User::factory()->create();
        $cartao = Cartao::factory()->create([
            'user_id' => $user->id,
            'saldo' => 3000.00,
        ]);
        $dadosDespesa = ['valor' => 50, 'cartao_id' => $cartao->id, 'descricao' => 'aluguel'];
        $response = $this->actingAs($user)->postJson('api/despesas', $dadosDespesa);
        $response->assertStatus(201);
    }

    //log($response->getContent());
    public function test_user_comum_nao_ve_despesas_de_outro_user()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $cartao = Cartao::factory()->create([
            'saldo' => 3000.00,
            'user_id' => $userLogado->id,
        ]);
        $despesa1 = Despesa::factory()->create([
            'cartao_id' => $cartao->id,
            'valor' => 50.00,
            'descricao' => 'Despesa do user logado',
        ]);

        $outroUser = User::factory()->create();
        $cartaoOutroUser = Cartao::factory()->create([
            'saldo' => 3000.00,
            'user_id' => $outroUser->id,
        ]);
        $despesa1Outro = Despesa::factory()->create([
            'cartao_id' => $cartaoOutroUser->id,
            'valor' => 50.00,
            'descricao' => 'Despesa do outro user',
        ]);

        $response = $this->actingAs($userLogado)->getJson('api/despesas/'.$despesa1Outro->id);
        $response->assertStatus(403);
    }

    public function test_admin_ve_todas_as_despesas()
    {
        $userLogado = User::factory()->create([
            'is_admin' => true,
        ]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $cartao1 = Cartao::factory()->create([
            'user_id' => $user1->id,
            'saldo' => 1561.98
        ]);
        $despesa1 = Despesa::factory()->create([
            'cartao_id' => $cartao1->id,
        ]);
        $cartao2 = Cartao::factory()->create([
            'user_id' => $user2->id,
            'saldo' => 1561.98
        ]);
        $despesa2 = Despesa::factory()->create([
            'cartao_id' => $cartao2->id,
        ]);
        $response = $this->actingAs($userLogado)->getJson('api/despesas');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $despesa1->id
        ]);
        $response->assertJsonFragment([
            'id' => $despesa2->id
        ]);
    }

    public function test_user_comum_nao_cadastra_despesas_no_cartao_de_outro_user(){
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $outroUser = User::factory()->create();
        $cartaoOutroUser = Cartao::factory()->create([
            'saldo' => 3000.00,
            'user_id' => $outroUser->id,
        ]);
        $despesaOutro = [
            'cartao_id' => $cartaoOutroUser->id,
            'valor' => 50.00,
            'descricao' => 'Despesa para outro user',
        ];
        $response = $this->actingAs($userLogado)->postJson('api/despesas', $despesaOutro);
        $response->assertStatus(404);
    }

    public function test_user_comum_nao_consegue_remover_despesas()
    {
        $userLogado = User::factory()->create([
            'is_admin' => false,
        ]);
        $outroUser = User::factory()->create();
        $cartaoOutroUser = Cartao::factory()->create([
            'saldo' => 3000.00,
            'user_id' => $outroUser->id,
        ]);
        $despesaOutro = Despesa::factory()->create([
            'cartao_id' => $cartaoOutroUser->id,
            'valor' => 50.00,
        ]);
        $response = $this->actingAs($userLogado)->deleteJson('api/despesas/'.$despesaOutro->id);
        $response->assertStatus(403);
    }

    public function test_admin_consegue_remover_despesas()
    {
        $userLogado = User::factory()->create([
            'is_admin'=>true
        ]);
        $outroUser = User::factory()->create();
        $cartaoOutroUser = Cartao::factory()->create([
            'user_id'=>$outroUser->id
        ]);
        $despesaOutro = Despesa::factory()->create([
            'cartao_id'=>$cartaoOutroUser->id,
        ]);
        $response = $this->actingAs($userLogado)->deleteJson('api/despesas/'.$despesaOutro->id);
        $response->assertStatus(204);
    }

    public function test_despesa_nao_encontrada_para_remover()
    {
        $userLogado = User::factory()->create([
            'is_admin'=>true
        ]);
        $cartao = Cartao::factory()->create([
            'user_id'=>$userLogado->id
        ]);
        $despesa = Despesa::factory()->create([
            'cartao_id'=>$cartao->id,
            'id'=>2
        ]);
        $response = $this->actingAs($userLogado)->deleteJson('api/despesas/99999');
        $response->assertStatus(404);
    }

    public function test_cadastrar_despesa_com_cartao_invalido()
    {
        $userLogado = User::factory()->create([]);
        $despesa = [
            'cartao_id'=>9999,
            'valor'=>100.00,
            'descricao'=>'Despesa teste',
        ];
        $response = $this->actingAs($userLogado)->postJson('api/despesas', $despesa);
        $response->assertStatus(422);
    }
}
