<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);
        $users = User::with('cartoes')->get();
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $userRequest)
    {
        try {
            //$userRequest->authorize();
            $user = $this->userService->cadastrarUser($userRequest->returndados());
            return response()->json($user, 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::with('cartoes')->findOrFail($id);
            Gate::authorize('view', $user);
            return response()->json($user, 200);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = $this->userService->atualizarUser($id, $request->returnDados());
            return response()->json($user, 200);
        }catch(ModelNotFoundException $e) {
            return response()->json([
                'messaqe' => 'Usuário não encontrado.'
            ], 404);
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Falha ao atualizar usuário.'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $userADeletar = User::findOrFail($id);
            Gate::authorize('delete', $userADeletar);
            $this->userService->removerUser($id);
            return response()->json(null, 204);
        }catch(ModelNotFoundException $e){
            return response()->json([
                'message' => 'Usuário não encontrado.'
            ], 404);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Falha ao remover usuário.'
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = $this->userService->autenticarUser($request->only('email', 'password'));
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
        }catch (ValidationException $e){
            return response()->json([
                'message' => 'Suas credenciais são inválidas!'
            ], 401);
        }catch (\Exception $e){
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
