<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/hello', function () {
    return response()->json(['message' => 'Test Successful!']);
});

Route::post('/messages/store', [ProjectController::class, 'messageStore']);


Route::post('/login', [AuthController::class, 'login']);
Route::get('/projects', [ProjectController::class, 'projectList']);
Route::get('/projects/{id?}', [ProjectController::class, 'projectDetails']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/projects/create', [ProjectController::class, 'createProject']);
    Route::post('/projects/store', [ProjectController::class, 'storeProject']);

});

