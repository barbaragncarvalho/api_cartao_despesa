<?php

namespace App\Services;

use App\Exceptions\CredenciaisInvalidasException;
use App\Exceptions\UserNaoEncontradoException;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;

class UserService
{
    public function listarUsers(int $paginate): Collection
    {
        $users = User::with('cartoes')->paginate($paginate);
        return $users->getCollection();
    }

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

    public function listarUmUser(string $id): User
    {
        $user = User::with('cartoes')->find($id);
        if(!$user){
            throw new UserNaoEncontradoException();
        }
        return $user;
    }

    public function atualizarUser(string $id, array $userAtualizado): User
    {
        try{
            $userEncontrado = $this->listarUmUser($id);
            $userAutenticado = Auth::user();
            if(!$userAutenticado->is_admin && isset($userAtualizado['is_admin'])){
                unset($userAtualizado['is_admin']);
            }
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
            User::destroy($id);
        }catch (\Exception $e){
            throw new \Exception("Falha ao remover usuário.");
        }
    }

    public function autenticarUser(array $dadosUser): User
    {
        $user = User::where('email', $dadosUser['email'])->first();
        if(!$user || !Hash::check($dadosUser['password'], $user->password)){
            throw new CredenciaisInvalidasException();
        }
        return $user;
    }
}
