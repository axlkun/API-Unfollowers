<?php

use App\Http\Controllers\Api\UsuariosController;
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

Route::post('store',[UsuariosController::class,'store']);

Route::get('unfollowers/{user}',[UsuariosController::class,'getUnfollowers']);

Route::get('unfollowing/{user}',[UsuariosController::class,'getNotFollowing']);
