<?php

namespace App\Exceptions;

class UserNotFoundException extends \Exception
{

    public function render($request){
        return response()->json(['message' => 'Usuário não encontrado.'], 404);
    }

}
