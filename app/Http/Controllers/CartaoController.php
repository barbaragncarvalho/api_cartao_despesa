<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartaoRequest;
use App\Http\Requests\UpdateCartaoRequest;
use App\Models\Cartao;
use App\Services\CartaoService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CartaoController extends Controller
{
    protected $cartaoService;
    public function __construct(CartaoService $cartaoService){
        $this->cartaoService = $cartaoService;
    }

    public function index()
    {
        $user = Auth::user();
        Gate::authorize('viewAny', Cartao::class);
        if($user->is_admin ?? false){
            $cartoes = Cartao::all();
        }else{
            $cartoes = $user->cartoes;
        }
        return response()->json($cartoes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartaoRequest $request)
    {
        $user = Auth::user();
        Gate::Authorize('create', [Cartao::class,$request->user_id]);
        try {
            $cartao = $this->cartaoService->cadastrarCartao($request->validated(), $user);
            return response()->json($cartao, 201);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Usuário dono do cartão não encontrado!'
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $cartao = Cartao::findOrFail($id);
            Gate::authorize('view', $cartao);
            return response()->json($cartao, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cartão não encontrado.'
            ], 404);
        }
    }

    public function update(UpdateCartaoRequest $request, string $id)
    {
        try {
            $cartao = Cartao::findOrFail($id);
            Gate::authorize('update', $cartao);
            $cartaoAtualizado = $this->cartaoService->atualizarCartao($id, $request->validated());
            return response()->json($cartaoAtualizado, 200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Cartão não encontrado.'
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao atualizar cartão: '.$e->getMessage()
            ], 400);
        }
    }


    public function destroy(string $id)
    {
        try {
            $cartao = Cartao::findOrFail($id);
            Gate::authorize('delete', $cartao);
            $this->cartaoService->removerCartao($id);
            return response()->json(null, 204);
        }catch (ModelNotFoundException $e){
            return response()->json([
                'message'=> 'Cartão não encontrado para ser removido.'
            ], 404);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover cartão: '.$e->getMessage()
            ], 400);
        }
    }
}
