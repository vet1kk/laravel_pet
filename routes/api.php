<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::apiResource('users', UserController::class);

Route::middleware('auth')->group(function () {
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');
    Route::get('/email/verify/{user}', [VerificationController::class, 'resendVerificationEmail'])->name('verification.resend')->middleware('throttle:6,1');
});
