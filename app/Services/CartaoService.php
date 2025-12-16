<?php

namespace App\Services;

use App\Models\Cartao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartaoService
{
    public function cadastrarCartao(array $dados): Cartao
    {
        $user = Auth::user();
        if(!$user->is_admin){
            $dados['user_id'] = $user->id;
        }
        try {
            $cartao = new Cartao();
            $cartao->fill($dados);
            $cartao->save();
            return $cartao;
        }catch(\Exception $e){
            throw new \Exception("Falha ao cadastrar cartao: ".$e->getMessage());
        }
    }

    public function atualizarCartao(string $id, array $cartaoAtualizado): Cartao
    {
        try {
            $cartaoEncontrado = Cartao::findOrFail($id);
            $cartaoEncontrado->update($cartaoAtualizado);
            return $cartaoEncontrado;
        }catch(\Exception $e){
            throw new \Exception("Falha ao atualizar cartao: ".$e->getMessage());
        }
    }

    public function removerCartao(string $id): void{
        try{
            $cartaoRemovido = Cartao::destroy($id);
            if(!$cartaoRemovido){
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Cartão não encontrado para ser removido.");
            }
        }catch(\Exception $e){
            throw new \Exception("Falha ao remover cartão.");
        }
    }
}
