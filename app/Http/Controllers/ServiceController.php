<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Models\Service;
use App\Models\UserType;
use App\Models\Worker;

class ServiceController extends Controller
{

    public function create(){
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

    public function find($id){
        $service = Service::find($id);
        if(! $service){
            throw new ServiceNotFoundException();
        }
        return response()->json($service);
    }

    public function delete($id){
        $service = Service::find($id);
        if(! $service){
            throw new ServiceNotFoundException();
        }
        $service->delete();
        return response()->json($service);
    }

    public function list(){
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

    public function update($id){
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
