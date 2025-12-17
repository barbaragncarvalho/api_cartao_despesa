<?php

namespace App\Services;

use App\Exceptions\CartaoInvalidoException;
use App\Exceptions\UserNaoEncontradoException;
use App\Models\Cartao;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class CartaoService
{
    public function listarCartoes(): Collection
    {
        $user = Auth::user();
        if(!$user){
            throw new UserNaoEncontradoException();
        }
        if($user->is_admin ?? false){
            $cartoes = Cartao::all();
        }else{
            $cartoes = $user->cartoes;
        }
        return $cartoes;
    }

    public function cadastrarCartao(array $dados): Cartao
    {
        $user = Auth::user();
        if(!$user){
            throw new UserNaoEncontradoException();
        }
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

    public function listarUmCartao(string $id): Cartao
    {
        $cartao = Cartao::find($id);
        if(!$cartao){
            throw new CartaoInvalidoException();
        }
        return $cartao;
    }

    public function atualizarCartao(string $id, array $cartaoAtualizado): Cartao
    {
        try {
            $cartaoEncontrado = Cartao::find($id);
            if(!$cartaoEncontrado){
                throw new CartaoInvalidoException();
            }
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
                throw new CartaoInvalidoException();
            }
        }catch(\Exception $e){
            throw new \Exception("Falha ao remover cartão: ".$e->getMessage());
        }
    }
}
