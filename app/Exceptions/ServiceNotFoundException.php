<?php

namespace App\Exceptions;

class ServiceNotFoundException extends \Exception
{

    public function render($request){
        return response()->json(['message' => 'Serviço não encontrado.'], 404);
    }

}
