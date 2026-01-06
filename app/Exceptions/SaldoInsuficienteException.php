<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class SaldoInsuficienteException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json(['error'=>'Operação negada', 'message'=> 'Você não tem saldo suficente para realizar esta transação.'], 402);
    }
}
