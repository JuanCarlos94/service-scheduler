<?php

namespace App\Http\Controllers;

use App\Exceptions\GeneratorAuthTokenException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\UserNotFoundException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @throws InvalidCredentialsException
     * @throws ValidationException
     * @throws GeneratorAuthTokenException
     */
    public function login(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = [
            'email' => $request->email,
            'password' => md5($request->password)
        ];
        $user = User::where($credentials)->first();
        if(! $user){
            throw new InvalidCredentialsException();
        }
        if(!$token = auth()->login($user)){
            throw new GeneratorAuthTokenException();
        }
        return $this->respondWithToken($token);
    }

    public function refresh(){
        $token = auth()->refresh();
        return $this->respondWithToken($token);
    }

    public function logout(){
        auth()->logout();
        return response()->json([
            'message' => 'Logout com sucesso!'
        ]);
    }

    public function me(){
        $user = auth()->user();
        if(! $user){
            throw new UserNotFoundException();
        }
        return response()->json($user);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

}
