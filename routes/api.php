<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Pets
Route::get('/adopt', [PetController::class, 'index']);
Route::post('/adopt', [PetController::class, 'store'])->middleware('auth:sanctum');
Route::get('/adopt/{id}', [PetController::class, 'show']);
Route::post('/adopt/{id}/favorite', [PetController::class, 'toggleFavorite'])->middleware('auth:sanctum');
Route::delete('/adopt/{id}', [PetController::class, 'destroy'])->middleware('auth:sanctum');

// RESTful API Routes
Route::get('/users/{id}', [UserController::class, 'show']);

// Pet Comments
Route::get('/adopt/{id}/comments', [\App\Http\Controllers\PetCommentController::class, 'index']);
Route::post('/adopt/{id}/comments', [\App\Http\Controllers\PetCommentController::class, 'store'])->middleware('auth:sanctum');
