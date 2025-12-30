<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AdoptionController;
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

// Pet Adoption
Route::post('/adopt/{id}/apply', [AdoptionController::class, 'store'])->middleware('auth:sanctum');

// Notifications
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);

    // User Applications
    Route::get('/user/applications', [\App\Http\Controllers\AdoptionApplicationController::class, 'index']);
    Route::get('/user/applications/sent', [\App\Http\Controllers\AdoptionApplicationController::class, 'sent']);
    Route::put('/user/applications/{id}', [\App\Http\Controllers\AdoptionApplicationController::class, 'update']);
});
