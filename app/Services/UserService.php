<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;

class UserService
{
    public function cadastrarUser(array $dados): User
    {
        if(isset($dados['password'])){
            $dados['password'] = Hash::make($dados['password']);
        }
        try{
            $user = new User();
            $user->fill($dados);
            $user->save();
            return $user;
        }catch(\Exception $e){
            throw new \Exception("Falha ao cadastrar usuário: ".$e->getMessage());
        }
    }

    public function atualizarUser(string $id, array $userAtualizado): User
    {

        try{
            $userEncontrado = User::findOrFail($id);
            if(isset($userAtualizado['password'])) {
                $userAtualizado['password'] = Hash::make($userAtualizado['password']);
            }
            $userEncontrado->update($userAtualizado);
            return $userEncontrado;
        }catch(\Exception $e){
            throw new \Exception("Falha ao atualizar usuário: ".$e->getMessage());
        }
    }

    public function removerUser(string $id): void
    {
        try{
            $userRemovido = User::destroy($id);
            if(!$userRemovido){
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Usuário não encontrado para ser removido.");
            }

        }catch (\Exception $e){
            throw new \Exception("Falha ao remover usuário.");
        }
    }

    public function autenticarUser(array $dadosUser): User
    {
        $user = User::where('email', $dadosUser['email'])->first();
        if(!$user || !Hash::check($dadosUser['password'], $user->password)){
            throw new ValidationException("Suas credenciais são inválidas!");
        }
        return $user;
    }
}
