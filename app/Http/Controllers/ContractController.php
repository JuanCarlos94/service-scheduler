<?php

namespace App\Http\Controllers;

use App\Exceptions\ContractNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Models\Contract;
use App\Models\ContractStatus;
use App\Models\UserType;
use Illuminate\Support\Facades\Gate;

class ContractController extends Controller
{

    /**
     * @OA\Post(
     *     path="/contracts",
     *     tags={"Contracts"},
     *     summary="Create a new contract",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *     ),
     *     @OA\Response(
     *         response=403,
     *          description="validation error"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function create(){
        if(Gate::denies('customer-only')){
            throw new UnauthorizedException();
        }
        $this->validate(request(), [
            'service_id' => 'required|exists:services,id',
            'value' => 'required|numeric',
            'scheduled_to' => 'required|date_format:Y-m-d H:i:s'
        ]);
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

    /**
     * @OA\Get(
     *     path="/contracts/{id}",
     *     tags={"Contracts"},
     *     summary="Get a contract by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Contract ID",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *          description="unauthorized"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function find($id){
        $user = auth()->user();
        switch($user->type){
            case UserType::CUSTOMER:
                $contract = Contract::where(['user_id' => $user->id, 'id' => $id])->first();
                break;
            case UserType::WORKER:
                $contract = Contract::where('id', $id)->whereHas('service', function($query) use($user) {
                    $query->where('worker_id', $user->worker->id);
                })->first();
                break;
            case UserType::ADMIN:
                $contract = Contract::find($id);
                break;
        }
        if(! $contract){
            throw new ContractNotFoundException();
        }
        return response()->json($contract);
    }

    /**
     * @OA\Delete(
     *     path="/contracts/{id}",
     *     tags={"Contracts"},
     *     summary="Delete a contract by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Contract ID",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *          description="unauthorized"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function delete($id){
        if(Gate::denies('admin-only')){
            throw new UnauthorizedException();
        }
        $contract = Contract::find($id);
        if(! $contract){
            throw new ContractNotFoundException();
        }
        $contract->delete();
        return response()->json(['message' => 'Contrato deletado com sucesso.']);
    }

    /**
     * @OA\Put(
     *     path="/contracts/{id}",
     *     tags={"Contracts"},
     *     summary="Update a contract",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Contract ID",
     *         required=true
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *     ),
     *     @OA\Response(
     *         response=403,
     *          description="validation error"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function update($id){
        if(Gate::denies('admin-only')){
            throw new UnauthorizedException();
        }
        $this->validate(request(), [
            'status' => 'required|in:'.ContractStatus::NEW.','.ContractStatus::CONFIRMED.','.ContractStatus::EXPIRED,
            'user_id' => 'required',
            'value' => 'required',
            'service_id' => 'required',
            'scheduled_to' => 'required|date_format:Y-m-d H:i:s',
            'user_confirmation' => 'required',
            'worker_confirmation' => 'required'
        ]);
        $updated = Contract::where('id', $id)
            ->update(request()->all());
        if($updated){
            $contract = Contract::find($id);
            return response()->json($contract);
        }
        return response()->json(['message' => 'Erro ao atualizar contrato, tente novamente.'], 500);
    }

    public function update_user_confirmation($id){
        $this->validate(request(), [
            'user_confirmation' => 'required'
        ]);
        $user = auth()->user();
        $contract = Contract::where(['id' => $id, 'user_id' => $user->id]);
        if(! $contract){
            throw new ContractNotFoundException();
        }
        $contract->user_confirmation = request()->user_confirmation;
        $contract->save();
        return response()->json($contract);
    }

    public function update_worker_confirmation($id){
        if(Gate::denies('worker-only')){
            throw new UnauthorizedException();
        }
        $this->validate(request(), [
            'worker_confirmation' => 'required'
        ]);
        $user = auth()->user();
        $contract = Contract::where('id', $id)->whereHas('service', function($query) use($user) {
            $query->where('worker_id', $user->worker->id);
        })->first();
        if(! $contract){
            throw new ContractNotFoundException();
        }
        $contract->worker_confirmation = request()->worker_confirmation;
        $contract->save();
        return response()->json($contract);
    }

    /**
     * @OA\Get(
     *     path="/contracts",
     *     tags={"Contracts"},
     *     summary="Return a list of contracts",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="user_id", default=1, type="integer"),
     *              @OA\Property (property="service_id", default=1, type="integer"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="scheduled_to", default="2022-12-01 12:30:00", type="datetime")
     *          )
     *     ),
     *     @OA\Response(
     *         response=401,
     *          description="unauthorized"
     *     ),
     *     security={
     *         {"api_key": {}}
     *     }
     * )
     */
    public function list(){
        if(Gate::denies('admin-only')){
            throw new UnauthorizedException();
        }
        $contracts = Contract::paginate(20);
        return response()->json($contracts);
    }

}
