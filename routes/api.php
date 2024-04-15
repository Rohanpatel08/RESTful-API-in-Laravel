<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostManagerController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('users', UserController::class);

Route::post('/login', [UserController::class, 'userLogin']);

Route::post('/post/create', [PostManagerController::class, 'createPost']);
Route::get('/posts', [PostManagerController::class, 'getPosts']);
Route::delete('/post/delete', [PostManagerController::class, 'deletePosts']);

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully.']);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/profile', function () {
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message', 'Verification link sent!']);
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
})->middleware(['auth', 'verified']);


Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? response()->json(['status' => __($status)])
        : response()->json(['email' => __($status)]);
})->middleware('guest');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
