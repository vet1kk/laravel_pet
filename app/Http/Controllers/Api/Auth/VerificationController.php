<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Get (
     *     path="/api/email/verify/{id}/{hash}",
     *     summary="Verify email",
     *     description="Verify email",
     *     operationId="verifyEmail",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *        name="id",
     *        in="path",
     *        required=true,
     *        description="User ID",
     *        @OA\Schema(
     *           type="integer",
     *           format="int64",
     *        )
     *     ),
     *     @OA\Parameter(
     *        name="hash",
     *        in="path",
     *        required=true,
     *        description="Hash for verification",
     *        @OA\Schema(
     *           type="string",
     *           format="string",
     *        )
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\JsonContent(
     *            type="string",
     *            example="Email verified successfully"
     *        )
     *     ),
     *     @OA\Response(
     *        response=401,
     *        description="Invalid hash",
     *     ),
     *     @OA\Response(
     *        response=422,
     *        description="Email already verified",
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="User not found",
     *     )
     * )
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));
        if ($user->hasVerifiedEmail()) {
            return response()->json('Email already verified!', 422);
        }
        if (!hash_equals(sha1($user->getEmailForVerification()), (string)$request->route('hash'))) {
            return response()->json('Invalid verification link!', 422);
        }
        $user->markEmailAsVerified();

        event(new Verified($user));


        return response()->json('Email verified successfully!');
    }

    /**
     * @OA\Get (
     *     path="/api/email/verify/{user}",
     *     summary="Resend verification email",
     *     description="Resend verification email",
     *     operationId="resendVerificationEmail",
     *     tags={"Auth"},
     *     @OA\Parameter(
     *        name="user",
     *        in="path",
     *        required=true,
     *        description="User ID",
     *        @OA\Schema(
     *            type="integer",
     *            format="int64"
     *        )
     *     ),
     *     @OA\Response(
     *        response=200,
     *        description="Success",
     *        @OA\JsonContent(
     *            type="string",
     *            example="Email verification link resent on your email!"
     *        )
     *     ),
     *     @OA\Response(
     *        response=422,
     *        description="Email already verified",
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="User not found",
     *     )
     * )
     */
    public function resendVerificationEmail(User $user): JsonResponse
    {
        if ($user->hasVerifiedEmail()) {
            return response()->json('Email already verified!', 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json('Email verification link resent on your email!');
    }
}

