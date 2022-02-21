<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Models\Service;
use App\Models\UserType;
use App\Models\Worker;
use Illuminate\Support\Facades\Gate;

class ServiceController extends Controller
{

    /**
     * @OA\Post(
     *     path="/services",
     *     tags={"Services"},
     *     summary="Create a new service",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default=200.00, type="integer"),
     *              @OA\Property (property="worker_id", default=2, type="integer")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default="200.00", type="integer"),
     *              @OA\Property (property="worker_id", default="2", type="integer")
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
        if(Gate::denies('worker-only')){
            throw new UnauthorizedException();
        }
        $this->validate(request(), [
           'title' => 'required|max:255',
           'description' => 'max:255',
           'value' => 'required|numeric'
        ]);

        $user = auth()->user();

        $service = new Service(request()->all());
        if($user->type == UserType::ADMIN){
            $service->worker_id = request()->worker_id;
        } else {
            $worker = Worker::where('user_id', $user->id)->first();
            if(! $worker){
                throw new UnauthorizedException();
            }
            $service->worker_id = $worker->id;
        }
        $service->save();
        return response()->json($service, 201);
    }

    /**
     * @OA\Get(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Get a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Service ID",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default="200.00", type="integer"),
     *              @OA\Property (property="worker_id", default="2", type="integer")
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
        $service = Service::find($id);
        if(! $service){
            throw new ServiceNotFoundException();
        }
        return response()->json($service);
    }

    /**
     * @OA\Delete(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Delete a service by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Service ID",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default="200.00", type="integer"),
     *              @OA\Property (property="worker_id", default="2", type="integer")
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
        if(Gate::denies('worker-only')){
            throw new UnauthorizedException();
        }
        $service = Service::find($id);
        if(! $service){
            throw new ServiceNotFoundException();
        }
        $service->delete();
        return response()->json($service);
    }

    /**
     * @OA\Get(
     *     path="/services",
     *     tags={"Services"},
     *     summary="Return a list of services",
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
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default="200.00", type="integer"),
     *              @OA\Property (property="worker_id", default="2", type="integer")
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
        if(Gate::denies('admin-only') && Gate::denies('worker-only')){
            throw new UnauthorizedException();
        }
        $user = auth()->user();
        if(! $user){
            throw new UnauthorizedException();
        }
        $worker = Worker::where('user_id', $user->id)->first();
        if(! $worker){
            throw new UnauthorizedException();
        }
        $services = Service::where('worker_id', $worker->id)->paginate(20);
        return response()->json($services);
    }

    /**
     * @OA\Put(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Update a service",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default="200.00", type="integer"),
     *              @OA\Property (property="worker_id", default="2", type="integer")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="title", default="Manutenção de computador", type="string"),
     *              @OA\Property (property="description", default="Formatação e backup de PC", type="string"),
     *              @OA\Property (property="value", default="200.00", type="integer"),
     *              @OA\Property (property="worker_id", default="2", type="integer")
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
        if(Gate::denies('admin-only') && Gate::denies('worker-only')){
            throw new UnauthorizedException();
        }
        $user = auth()->user();

        if($user->type == UserType::ADMIN){
            $service = Service::where(['id' => $id])->first();
        } else {
            $worker = Worker::where('user_id', $user->id)->first();
            if(! $worker){
                throw new UnauthorizedException();
            }
            $service = Service::where(['worker_id' => $worker->id, 'id' => $id])->first();
        }
        if(! $service){
            throw new ServiceNotFoundException();
        }
        $serviceId = $service->update(request()->only($user->type == UserType::ADMIN ? ['title', 'description', 'value', 'worker_id'] : ['title', 'description', 'value']));
        $service = Service::find($serviceId);
        return response()->json($service);
    }

}
