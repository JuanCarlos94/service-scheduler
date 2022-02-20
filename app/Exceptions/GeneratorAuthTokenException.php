<?php

namespace App\Exceptions;

class GeneratorAuthTokenException extends \Exception
{
    public function render($request){
        return response()->json(['message' => 'Erro ao gerar token de autenticação.'], 401);
    }
}
