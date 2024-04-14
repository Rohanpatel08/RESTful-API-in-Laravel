<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostManagerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('users', UserController::class);

Route::post('/login', [UserController::class, 'userLogin']);

Route::post('/post/create', [PostManagerController::class, 'post']);
Route::get('/posts', [PostManagerController::class, 'getPosts']);
Route::delete('/post/delete', [PostManagerController::class, 'deletePosts']);
