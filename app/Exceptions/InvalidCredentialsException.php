<?php

namespace App\Exceptions;

class InvalidCredentialsException extends \Exception
{

    public function render($request){
        return response()->json(['message' => 'Credenciais inválidas.'], 404);
    }
}
