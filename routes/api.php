<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CronController;

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

Route::post('register', [UserController::class, 'register']);

Route::post('login', [UserController::class, 'login']);

Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/user/{id}/subscription', [UserController::class, 'createSubscription'])->middleware('auth:sanctum');

Route::put('/user/{id}/subscription/{subscriptionId}', [UserController::class, 'updateSubscription'])->middleware('auth:sanctum');

Route::delete('/user/{id}/subscription', [UserController::class, 'deleteSubscription'])->middleware('auth:sanctum');

Route::post('/user/{id}/transaction', [UserController::class, 'createTransaction'])->middleware('auth:sanctum');

Route::get('/user/{id}', [UserController::class, 'getUserWithDetails'])->middleware('auth:sanctum');

Route::get('/cron', [CronController::class, 'cron']);
