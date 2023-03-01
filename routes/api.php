<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\VerificationController;
use App\Http\Controllers\Api\UserController;
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
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])->name('verification.verify');
Route::get('/email/verify/{user}', [VerificationController::class, 'resendVerificationEmail'])->name('verification.resend')->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('users', UserController::class);

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});
