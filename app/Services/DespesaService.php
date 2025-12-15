<?php

namespace App\Services;

use App\Mail\DespesaCriada;
use App\Models\Cartao;
use App\Models\Despesa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use function PHPUnit\Framework\throwException;

class DespesaService
{
    public function cadastrarDespesa(array $dados, User $user){

        return DB::transaction(function () use ($dados, $user) {
            $cartao = Cartao::where('id', $dados['cartao_id'])
                            ->where('user_id',$user->id)->first();
            if (!$cartao) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Cartão inválido ou não encontrado.');
            }
            if ($cartao->saldo < $dados['valor']) {
                throw new \Exception('Saldo insuficiente no cartão.');
            }
            $despesa = Despesa::create($dados);
            $cartao->saldo -= $dados['valor'];
            $cartao->save();

            return $despesa;
        });
    }

    public function removerDespesa(string $id): void
    {
        DB::transaction(function () use ($id) {
            $despesa = Despesa::with('cartao')->findOrFail($id);
            $cartao = $despesa->cartao;
            $valorDespesa = (float)$despesa->valor;
            $saldoAtual = (float)$cartao->saldo;
            $cartao->saldo = $saldoAtual + $valorDespesa;
            $cartao->save();
            $despesa->delete();
        });
    }
}
