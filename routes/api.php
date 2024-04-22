<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostManagerController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('users', UserController::class);
Route::post('user/followers/create', [UserController::class, 'followers']);
Route::get('user/followers', [UserController::class, 'getFollowersByUserId']);
Route::post('user/followings/create', [UserController::class, 'followings']);
Route::get('user/followings', [UserController::class, 'getFollowingsByUserId']);

Route::post('/login', [UserController::class, 'userLogin']);
Route::get('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout']);

Route::post('/post/create', [PostManagerController::class, 'createPost']);
Route::get('/posts', [PostManagerController::class, 'getPostsByUser']);
Route::get('/post/search', [PostManagerController::class, 'searchPost']);
Route::get('/all/posts', [PostManagerController::class, 'getPosts']);
Route::get('/post/update', [PostManagerController::class, 'updatePost']);
Route::delete('/post/delete', [PostManagerController::class, 'deletePosts']);

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest  $request) {
    $request->fulfill();

    return response()->json(["message" => "verified"]);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $email = User::where('email', $request->email)->first();
    if ($email == null) {
        return response()->json([
            'error' => [
                'message' => 'This user is not found.'
            ]
        ]);
    }
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
