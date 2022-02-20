<?php

namespace App\Http\Controllers;

use App\Exceptions\ContractNotFoundException;
use App\Models\Contract;
use App\Models\UserType;

class ContractController extends Controller
{

    public function create(){
        $user = auth()->user();
        $contract = new Contract(request()->all());

        if($user->type == UserType::ADMIN){
            $contract->user_id = request()->user_id;
        } else {
            $contract->user_id = $user->id;
        }

        $contract->save();
        return response()->json($contract, 201);
    }

    public function find($id){
        $user = auth()->user();
        switch($user->type){
            case UserType::CUSTOMER:
                $contract = Contract::where(['user_id' => $user->id, 'id' => $id])->first();
                break;
            case UserType::WORKER:
                $contract = Contract::where(['service.worker.user_id' => $user->id, 'id' => $id])->first();
                break;
            case UserType::ADMIN:
                $contract = Contract::find($id);
                break;
        }
        if(! $contract){
            throw new ContractNotFoundException();
        }
    }

}
