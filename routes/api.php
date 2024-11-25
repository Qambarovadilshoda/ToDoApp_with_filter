<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('tasks', TaskController::class);
    Route::get('/filter/tasks', [TaskController::class, 'filterTask']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/mark-done/{id}', [TaskController::class, 'markDone']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
