<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::apiResource('/users', UserController::class);

Route::prefix('/users')->group(function () {

Route::get('trashed', [UserController::class, 'trashed']);
    Route::patch('{user}/restore', [UserController::class, 'restore']);
    Route::delete('{user}/force', [UserController::class, 'forceDelete']);

});