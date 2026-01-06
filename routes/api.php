<?php

use App\Http\Controllers\CartaoController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
#Cria as rotas todas automaticamente
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/cartoes', CartaoController::class)->parameters(['cartoes' => 'cartao']);
    Route::apiResource('/despesas', DespesaController::class)->only(['store', 'show', 'index', 'destroy']);
});
