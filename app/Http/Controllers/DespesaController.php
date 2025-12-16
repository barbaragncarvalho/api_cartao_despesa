<?php
namespace App\Http\Controllers;
use App\Events\EventoMail;
use App\Http\Requests\StoreDespesaRequest;
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
    public function index()
    {
        $user = Auth::user();
        Gate::authorize('viewAny', Despesa::class);

        if ($user->is_admin ?? false) {
            $despesas = Despesa::all();
        } else {
            $cartaoIds = $user->cartoes->pluck('id');
            $despesas = Despesa::whereIn('cartao_id', $cartaoIds)->get();
        }
        return response()->json($despesas, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDespesaRequest $request, DespesaService $despesaService)
    {
        try {
            $despesa = $despesaService->cadastrarDespesa($request->returnDados());

            $adminsEmails = User::where('is_admin', true)->pluck('email')->toArray();
            $destinatarios = array_merge([$request->user()->email], $adminsEmails);
            $despesaComCartao = $despesa->load('cartao');
            Mail::to($destinatarios)->send(new DespesaCriada($despesaComCartao));
            return response()->json($despesa, 201);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Cartão inválido ou não encontrado.'
            ], 404);
        }
        catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
    }
    }

    public function show(string $id)
    {
        try {
            $despesa = Despesa::findOrFail($id);
            Gate::authorize('view', $despesa);
            return response()->json($despesa, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Despesa não encontrada.'
            ], 404);
        }
    }

    public function destroy(string $id, DespesaService $despesaService)
    {
        try {
            $despesa = Despesa::findOrFail($id);
            Gate::authorize('delete', $despesa);
            $despesaService->removerDespesa($id);
            return response()->json(null, 204);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Despesa não encontrada para ser removida.'
            ], 404);
        }catch(\Illuminate\Auth\Access\AuthorizationException $e){
            return response()->json([
                'message' => 'Você não tem permissão para remover despesa.'
            ], 403);
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Falha ao remover despesa:'. $e->getMessage()
            ], 400);
        }
    }
}
