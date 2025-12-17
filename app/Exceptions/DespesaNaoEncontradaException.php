<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class DespesaNaoEncontradaException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json(['message'=>'A despesa não foi encontrada.'], 404);
    }
}
