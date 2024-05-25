<?php

use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/posts", [PostController::class, "index"]);
    Route::post("/posts/create", [PostController::class, "store"]);
    Route::put("/posts/update/{id}", [PostController::class, "update"]);
    Route::delete("/posts/delete/{id}", [PostController::class, "delete"]);
    Route::get("/posts/{id}", [PostController::class, "show"]);

    Route::post('/posts/{id}/like', [LikeController::class, "store"]);
    Route::delete('/posts/{id}/like', [LikeController::class, "destroy"]);
});
