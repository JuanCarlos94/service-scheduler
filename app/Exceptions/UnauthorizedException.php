<?php

namespace App\Exceptions;

class UnauthorizedException extends \Exception
{

    public function render($request){
        return response()->json(['message' => 'Acesso não autorizado.'], 404);
    }

}
