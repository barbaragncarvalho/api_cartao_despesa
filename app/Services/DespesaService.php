<?php

namespace App\Services;

use App\Exceptions\CartaoInvalidoException;
use App\Exceptions\SaldoInsuficienteException;
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
    public function cadastrarDespesa(array $dados){
        $user = Auth::user();
        return DB::transaction(function () use ($dados, $user) {
            $consulta = Cartao::where('id', $dados['cartao_id']);
            if(!$user->is_admin){
                $consulta->where('user_id',$user->id);
            }
            $cartao = $consulta->first();
            if (!$cartao) {
                throw new CartaoInvalidoException();
            }
            if ($cartao->saldo < $dados['valor']) {
                throw new SaldoInsuficienteException();
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
