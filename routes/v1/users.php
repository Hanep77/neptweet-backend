<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware("guest")->group(function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get("/me", [UserController::class, 'me']);
    Route::get("/users/search", [UserController::class, 'search']);
    Route::get("/users/{id}", [UserController::class, 'show']);
    Route::post('/logout', [UserController::class, 'logout']);
});
