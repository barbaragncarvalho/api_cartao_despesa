<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UserNaoEncontradoException extends Exception
{
    public function render($request): JsonResponse
    {
        return response()->json(['message'=>'O user não foi encontrado.'], 404);
    }
}
