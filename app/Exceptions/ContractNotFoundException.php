<?php

namespace App\Exceptions;

class ContractNotFoundException extends \Exception
{

    public function render($request){
        return response()->json(['message' => 'Contrato não encontrado.'], 404);
    }

}
