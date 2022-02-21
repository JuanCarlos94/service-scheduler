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
     * @OA\Post(
     *     path="/login",
     *     tags={"Authentication"},
     *     summary="Authenticate a user for access the application",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property (property="email", default="joao@mail.com", type="integer"),
     *              @OA\Property (property="password", default="secret", type="string")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="access_token", default="", type="integer"),
     *              @OA\Property (property="token_type", default="bearer", type="string"),
     *              @OA\Property (property="expires_in", default="3600", type="integer"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="invalid credentials"
     *     ),
     *     @OA\Response(
     *         response=401,
     *          description="token generation error"
     *     )
     * )
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

    /**
     * @OA\Post(
     *     path="/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh a bearer token created",
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="access_token", default="", type="integer"),
     *              @OA\Property (property="token_type", default="bearer", type="string"),
     *              @OA\Property (property="expires_in", default="3600", type="integer"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="invalid credentials"
     *     ),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function refresh(){
        $token = auth()->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Authentication"},
     *     summary="Invalid a bearer token",
     *     @OA\Response(
     *         response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property (property="message", default="Logout com sucess.", type="string")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="invalid credentials"
     *     ),
     *      security={{ "apiAuth": {} }}
     * )
     */
    public function logout(){
        auth()->logout();
        return response()->json([
            'message' => 'Logout com sucesso!'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/me",
     *     tags={"Authentication"},
     *     summary="Return the user token owner",
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
     *         response=404,
     *          description="invalid credentials"
     *     ),
     *      security={{ "apiAuth": {} }}
     * )
     */
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
