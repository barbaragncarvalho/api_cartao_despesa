<?php
namespace App\Http\Controllers;
use App\Events\EventoMail;
use App\Exceptions\PermissaoNegadaDeAcessoException;
use App\Http\Requests\StoreDespesaRequest;
use App\Http\Requests\ViewAllDespesaRequest;
use App\Mail\DespesaCriada;
use App\Models\Despesa;
use App\Models\User;
use App\Services\DespesaService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class DespesaController extends Controller
{
    public function index(ViewAllDespesaRequest $request, DespesaService $despesaService)
    {
        $paginate = $request->getPaginate();
        $despesas = $despesaService->listarDespesas($paginate);
        return response()->json($despesas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDespesaRequest $request, DespesaService $despesaService)
    {
        $despesa = $despesaService->cadastrarDespesa($request->returnDados(), $request->user());
        return response()->json($despesa, 201);
    }

    public function show(string $id, DespesaService $despesaService)
    {
        $despesa = $despesaService->listarUmaDespesa($id);
        Gate::authorize('view', $despesa);
        return response()->json($despesa, 200);
    }

    public function destroy(string $id, DespesaService $despesaService)
    {
        $despesa = $despesaService->listarUmaDespesa($id);
        Gate::authorize('delete', $despesa);
        $despesaService->removerDespesa($id);
        return response()->json(null, 204);
    }
}
