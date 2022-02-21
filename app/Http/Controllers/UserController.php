<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use App\Models\UserType;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Create a new user",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string"),
     *              @OA\Property (property="zip_code", default="00000-000", type="string")
     *          )
     *      ),
     *     @OA\Response(
     *         response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="id", default="1", type="integer"),
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string"),
     *              @OA\Property (property="zip_code", default="00000-000", type="string")
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
    public function create(Request $request)
    {
        $request = $this->sanatize_input($request);
        $validator = $this->configCreateValidator($request->all());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = new User($request->all());
        $user->password = $request->password;
        if (auth()->user() != null && auth()->user()->type == UserType::ADMIN) {
            $user->type = $request->type;
        }
        $user->save();

        if ($request->type == UserType::WORKER) {
            $worker = new Worker(['user_id' => $user->id]);
            $worker->save();
        }

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Get a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="id", default="1", type="integer"),
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string")
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
    public function find($id)
    {
        if (Gate::denies('admin-only', $id)) {
            throw new UnauthorizedException();
        }
        $user = User::find($id);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return response()->json($user);
    }

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Get a list of users",
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
     *              @OA\Property (property="id", default="1", type="integer"),
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string")
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
    public function list()
    {
        if (Gate::denies('admin-only')) {
            throw new UnauthorizedException();
        }
        $users = User::paginate(20);
        return response()->json($users);
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Update a user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string"),
     *              @OA\Property (property="zip_code", default="00000-000", type="string")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="id", default="1", type="integer"),
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string"),
     *              @OA\Property (property="zip_code", default="00000-000", type="string")
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
    public function update(Request $request, $id)
    {
        if (Gate::denies('admin-or-user-himself', $id)) {
            throw new UnauthorizedException();
        }
        $request = $this->sanatize_input($request);
        $validator = $this->configUpdateValidator($id, $request->all());
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::find($id);
        if (! $user) {
            throw new UserNotFoundException();
        }
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = request()->password;
        $user->cellphone = request()->cellphone;
        $user->address = request()->address;
        $user->city = request()->city;
        $user->state = request()->state;
        $user->zip_code = request()->zip_code;
        $user->save();
        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     tags={"Users"},
     *     summary="Delete a user by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="id", default="1", type="integer"),
     *              @OA\Property (property="name", default="João", type="string"),
     *              @OA\Property (property="email", default="joao@mail.com", type="string"),
     *              @OA\Property (property="password", default="secret", type="string"),
     *              @OA\Property (property="password_confirmation", default="secret", type="string"),
     *              @OA\Property (property="cellphone", default="(00) 90000-0000", type="string"),
     *              @OA\Property (property="address", default="Rua das memórias, N 10, Bairro Achados", type="string"),
     *              @OA\Property (property="city", default="Teresina", type="string"),
     *              @OA\Property (property="state", default="PI", type="string")
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
    public function delete($id)
    {
        if (Gate::denies('admin-only')) {
            throw new UnauthorizedException();
        }
        $user = User::find($id);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $user->delete();
        return response()->json($user);
    }

    private function sanatize_input($request)
    {
        $request->merge(array(
                'zip_code' => preg_replace("/[^0-9]+/i", '', $request->zip_code),
                'cellphone' => preg_replace("/[^0-9]+/i", '', $request->cellphone))
        );
        return $request;
    }

    private function configCreateValidator($data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6|max:10',
            'cellphone' => 'required|unique:users|regex:/[0-9]{11}/',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:2',
            'zip_code' => 'required|size:8',
            'type' => 'required|in:'.UserType::ADMIN.','.UserType::CUSTOMER.','.UserType::WORKER
        ], [
            'zip_code.required' => 'O campo CEP é obrigatório.',
            'zip_code.size' => 'CEP deve ter 8 dígitos.',
            'cellphone.required' => 'O campo celular é obrigatório.',
            'cellphone.size' => 'O celular deve possuir 11 dígitos.',
            'cellphone.unique' => 'Celular já cadastrado.',
            'cellphone.regex' => 'Celular tem um formato inválido.',
        ])->stopOnFirstFailure(true);
    }

    private function configUpdateValidator($id, $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'required|confirmed|min:6|max:10',
            'cellphone' => 'required|regex:/[0-9]{11}/|unique:users,cellphone,' . $id,
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'state' => 'required|max:2',
            'zip_code' => 'required|size:8',
            'type' => 'required|in:'.UserType::ADMIN.','.UserType::CUSTOMER.','.UserType::WORKER
        ], [
            'zip_code.required' => 'O campo CEP é obrigatório.',
            'zip_code.size' => 'CEP deve ter 8 dígitos.',
            'cellphone.required' => 'O campo celular é obrigatório.',
            'cellphone.size' => 'O celular deve possuir 11 dígitos.',
            'cellphone.regex' => 'Celular tem um formato inválido.',
        ])->stopOnFirstFailure(true);
    }

}
