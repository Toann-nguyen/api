<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Route::prefix('/users')->group(function () {

//     Route::get('trashed', [UserController::class, 'trashed']);
//     Route::patch('{user}/restore', [UserController::class, 'restore']);
//     Route::delete('{user}/force', [UserController::class, 'forceDelete']);

// });

// Route::middleware('auth:sanctum',  () {
//     Route::apiResource('/users', UserController::class);
// });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});