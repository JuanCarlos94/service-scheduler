<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Models\UserType;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $request = $this->sanatize_input($request);
        $validator = $this->configCreateValidator($request->all());
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $user = new User($request->all());
        $user->setPassword($request->password);
        if(auth()->user() != null && auth()->user()->type == UserType::ADMIN){
            $user->type = $request->type;
        }
        $user->save();

        if($request->type == UserType::WORKER){
            $worker = new Worker(['user_id' => $user->id]);
        }

        return response()->json($user, 201);
    }

    /**
     * @throws UserNotFoundException
     */
    public function find($id){
        $user = User::find($id);
        if(!$user){
            throw new UserNotFoundException();
        }
        return response()->json($user);
    }

    public function list(){
        $users = User::paginate(20);
        return response()->json($users);
    }

    public function update(Request $request, $id){
        $request = $this->sanatize_input($request);
        $validator = $this->configUpdateValidator($id, $request->all());
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $userId = DB::table('users')
            ->where('id', $id)
            ->update($request->all(['name', 'email', 'password', 'cellphone', 'address', 'city', 'state', 'zip_code', 'type']));
        $user = User::find($userId);
        if(!$user){
            throw new UserNotFoundException();
        }
        return response()->json($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function delete($id){
        $user = User::find($id);
        if(!$user){
            throw new UserNotFoundException();
        }
        $user->delete();
        return response()->json($user);
    }

    private function sanatize_input($request){
        $request->merge(array(
            'zip_code' => preg_replace("/[^0-9]+/i",'', $request->zip_code),
            'cellphone' => preg_replace("/[^0-9]+/i",'', $request->cellphone))
        );
        return $request;
    }

    private function configCreateValidator($data){
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6|max:10',
            'cellphone' => 'required|unique:users|regex:/[0-9]{11}/',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:2',
            'zip_code' => 'required|size:8'
        ], [
            'zip_code.required' => 'O campo CEP é obrigatório.',
            'zip_code.size' => 'CEP deve ter 8 dígitos.',
            'cellphone.required' => 'O campo celular é obrigatório.',
            'cellphone.size' => 'O celular deve possuir 11 dígitos.',
            'cellphone.unique' => 'Celular já cadastrado.',
            'cellphone.regex' => 'Celular tem um formato inválido.',
        ])->stopOnFirstFailure(true);
    }

    private function configUpdateValidator($id, $data){
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'password' => 'required|confirmed|min:6|max:10',
            'cellphone' => 'required|regex:/[0-9]{11}/|unique:users,cellphone,'.$id,
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:2',
            'zip_code' => 'required|size:8'
        ], [
            'zip_code.required' => 'O campo CEP é obrigatório.',
            'zip_code.size' => 'CEP deve ter 8 dígitos.',
            'cellphone.required' => 'O campo celular é obrigatório.',
            'cellphone.size' => 'O celular deve possuir 11 dígitos.',
            'cellphone.regex' => 'Celular tem um formato inválido.',
        ])->stopOnFirstFailure(true);
    }

}
