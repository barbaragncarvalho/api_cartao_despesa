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
use Illuminate\Support\Facades\Log;

class CartaoController extends Controller
{
    protected $cartaoService;
    public function __construct(CartaoService $cartaoService){
        $this->cartaoService = $cartaoService;
    }

    public function index()
    {
        Gate::authorize('viewAny', Cartao::class);
        $cartoes = $this->cartaoService->listarCartoes();
        return response()->json($cartoes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCartaoRequest $request)
    {
        $cartao = $this->cartaoService->cadastrarCartao($request->returnDados());
        return response()->json($cartao, 201);
    }

    public function show(string $id)
    {
        $cartao = $this->cartaoService->listarUmCartao($id);
        Gate::authorize('view', $cartao);
        return response()->json($cartao, 200);
    }

    public function update(UpdateCartaoRequest $request, string $id)
    {
        $cartaoAtualizado = $this->cartaoService->atualizarCartao($id, $request->returnDados());
        return response()->json($cartaoAtualizado, 200);
    }


    public function destroy(string $id)
    {
        $cartao = $this->cartaoService->listarUmCartao($id);
        Gate::authorize('delete', $cartao);
        $this->cartaoService->removerCartao($id);
        return response()->json(null, 204);
    }
}
