<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->group(function () {
    Route::get("/comments", [CommentController::class, "index"]);
    Route::post("/comments/create", [CommentController::class, "store"]);
    Route::put("/comments/update/{id}", [CommentController::class, "update"]);
    Route::delete("/comments/delete/{id}", [CommentController::class, "delete"]);
});
