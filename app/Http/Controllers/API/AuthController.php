<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

  /**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Realiza o login de um usuário",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string", example="email@exemplo.com"),
 *             @OA\Property(property="password", type="string", example="senha123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login bem-sucedido",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Nome do Usuário"),
 *                 @OA\Property(property="email", type="string", example="email@exemplo.com"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-06-02T12:00:00Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-06-02T12:00:00Z")
 *             ),
 *             @OA\Property(property="token", type="string", example="token_de_autenticação"),
 *             @OA\Property(property="message", type="string", example="Login bem-sucedido")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Credenciais inválidas",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Credenciais inválidas")
 *         )
 *     )
 * )
 */
    public function login(LoginRequest $request)
    {
       
        $credentials = $request->only('email', 'password');
       
        $token = Auth::attempt($credentials);
        
        if (!$token) {
            return (new UserResource(null, $token, 'Unauthorized'))
            ->response()
            ->setStatusCode(401);
        }

        $user = Auth::user();

        return (new UserResource($user, $token, 'Login successful'));
    }
     

    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Registra um novo usuário",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Nome do Usuário"),
 *             @OA\Property(property="email", type="string", example="email@exemplo.com"),
 *             @OA\Property(property="password", type="string", example="senha123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Registro bem-sucedido",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="message", type="string", example="Registro bem-sucedido")
 *         )
 *     )
 * )
 */

    public function register(RegisterRequest $request)
    {
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return (new UserResource($user, null, 'Register successful'));
       
    }

        /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Realiza o logout de um usuário autenticado",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout bem-sucedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout bem-sucedido")
     *         )
     *     )
     * )
     */

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);

        return (new UserResource(Auth::user(), null, 'Successfully logged out'));
    }

    /**
 * @OA\Post(
 *     path="/api/refresh",
 *     summary="Renova o token de autenticação",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Token renovado",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             ),
 *             @OA\Property(property="authorisation", type="object",
 *                 @OA\Property(property="token", type="string"),
 *                 @OA\Property(property="type", type="string", example="bearer")
 *             ),
 *             @OA\Property(property="message", type="string", example="Token renovado")
 *         )
 *     )
 * )
 */

    public function refresh()
    {
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);

        return (new UserResource(Auth::user(), Auth::refresh(), 'Renewed token'));
    }

    
}
