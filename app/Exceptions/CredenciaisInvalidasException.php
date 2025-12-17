<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CredenciaisInvalidasException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json(['message'=>'Suas credenciais são inválidas!.'], 401);
    }
}
