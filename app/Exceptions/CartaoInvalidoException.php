<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

class CartaoInvalidoException extends \Exception
{
    public function render($request): JsonResponse
    {
    return response()->json(['error'=>'Operação negada', 'message'=>'Cartão inválido ou não encontrado.'], 404);
    }
}
