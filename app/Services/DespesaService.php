<?php

namespace App\Services;

use App\Exceptions\CartaoInvalidoException;
use App\Exceptions\DespesaNaoEncontradaException;
use App\Exceptions\SaldoInsuficienteException;
use App\Http\Requests\StoreDespesaRequest;
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
    public function listarDespesas(User $user)
    {
        if ($user->is_admin) {
            return Despesa::all();
        }
        return Despesa::whereIn('cartao_id',$user->cartoes->pluck('id'))->get();
    }

    public function cadastrarDespesa(array $dados, User $userLogado){
        return DB::transaction(function () use ($dados, $userLogado) {
            $consulta = Cartao::where('id', $dados['cartao_id']);
            if(!$userLogado->is_admin){
                $consulta->where('user_id',$userLogado->id);
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
            $adminsEmails = User::where('is_admin', true)->pluck('email')->toArray();
            $destinatarios = array_merge([$userLogado->email], $adminsEmails);
            $despesaComCartao = $despesa->load('cartao');
            Mail::to($destinatarios)->send(new DespesaCriada($despesaComCartao));
            return $despesa;
        });
    }

    public function listarUmaDespesa(string $id): Despesa
    {
        $despesa = Despesa::find($id);
        if(!$despesa){
            throw new DespesaNaoEncontradaException();
        }
        return $despesa;
    }

    public function removerDespesa(string $id): void
    {
        try {
            DB::transaction(function () use ($id) {
                $despesa = $this->listarUmaDespesa($id);
                $cartao = $despesa->cartao;
                $cartao->increment('saldo', $despesa->valor);
                $despesa->delete();
            });
        }catch (\Illuminate\Database\QueryException $e) {
            throw new \Illuminate\Database\QueryException('Erro no banco de dados ao incrementar saldo do cartão.');
        }
    }
}
