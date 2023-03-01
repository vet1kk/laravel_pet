<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\StoreUserRequest as RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="register",
     *      tags={"Auth"},
     *      summary="Register",
     *      description="Register a new user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="string",
     *              example="We send you an email to verify your account. Please check your inbox"
     *          )
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        event(new Registered($user));

        return response()->json("We send you an email to verify your account. Please check your inbox", 201);
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Login",
     *      description="Login a user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 example="9|7VRWLzUicZSANOfVqnBrUS5QYGZt24awh76e74SS"
     *              ),
     *          )
     *      ),
     *      @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json("These credentials do not match our records", 422);
        }
        $user = User::filterEmail($request->email)->first();

        if (!$user->hasVerifiedEmail()) {
            return response()->json("User email not verified", 403);
        }

        $token = $user->createToken('access_token')->plainTextToken;
        return response()->json(['access_token' => $token]);
    }

    /**
     * @OA\Get(
     *      path="/api/logout",
     *      operationId="logout",
     *      tags={"Auth"},
     *      summary="Logout",
     *      description="Logout a user",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="string",
     *              example="User successfully logged out"
     *          )
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json("User successfully logged out");
    }
}
