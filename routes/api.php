<?php

use Illuminate\Http\Request;
use App\Http\Controllers\PrivyController;
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

/*
|--------------------------------------------------------------------------
| PrivyId Routes
|--------------------------------------------------------------------------
*/
Route::get('/privy/token', [PrivyController::class, 'getToken']);
Route::post('/privy/register', [PrivyController::class, 'register']);
Route::post('/privy/register/resend', [PrivyController::class, 'resendRegister']);
Route::post('/privy/register/status', [PrivyController::class, 'checkRegisterStatus']);
