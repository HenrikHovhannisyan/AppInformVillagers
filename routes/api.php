<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/send-verification-code', [PhoneVerificationController::class, 'sendVerificationCode']);
Route::post('/verify-code', [PhoneVerificationController::class, 'verifyCode']);
Route::post('/login', [PhoneVerificationController::class, 'login']);
Route::post('user/{user}/update', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'show']);
Route::middleware('auth:sanctum')->get('/account', [AccountController::class, 'show']);
Route::middleware('auth:sanctum')->put('account', [AccountController::class, 'update']);
Route::middleware('auth:sanctum')->put('/account/approve', [AccountController::class, 'approveAccount']);
