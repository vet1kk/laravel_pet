<?php

namespace App\Http\Controllers\Api;

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
     */

    public function verifyEmail(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));
        if (!hash_equals(sha1($user->getEmailForVerification()), (string)$request->route('hash'))) {
            return response()->json('Invalid verification link!', 422);
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return response()->json('Email verified!');
    }

    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->route('id'));
        if ($user->hasVerifiedEmail()) {
            return response()->json('User already have verified email!', 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json('Email verification link resent on your email!');
    }
}

