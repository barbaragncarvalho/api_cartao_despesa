<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ViewAllUsersRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ViewAllUsersRequest $request)
    {
        $paginate = $request->getPaginate();
        $users = $this->userService->listarUsers($paginate);
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $userRequest)
    {
        $user = $this->userService->cadastrarUser($userRequest->returndados());
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = $this->userService->listarUmUser($id);
        Gate::authorize('view', $user);
        return response()->json($user, 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = $this->userService->atualizarUser($id, $request->returnDados());
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userADeletar = $this->userService->listarUmUser($id);
        Gate::authorize('delete', $userADeletar);
        $this->userService->removerUser($id);
        return response()->json(null, 204);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
            $user = $this->userService->autenticarUser($request->only('email', 'password'));
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);
    }
}
