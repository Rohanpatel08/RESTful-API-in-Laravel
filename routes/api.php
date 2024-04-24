<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostManagerController;
use App\Http\Controllers\VerificationController;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::resource('users', UserController::class);
Route::post('user/followers/create', [UserController::class, 'followers']);
Route::post('user/followers/remove', [UserController::class, 'removeFollowers']);

Route::post('user/followings/create', [UserController::class, 'followings']);
Route::post('user/followings/remove', [UserController::class, 'removeFollowings']);


Route::post('/login', [UserController::class, 'userLogin']);
Route::post('/logout', [UserController::class, 'logout']);


Route::get('posts/comments/{comment}', [CommentController::class, 'show']);
Route::post('posts/{post}/comment', [CommentController::class, 'store']);
Route::put('posts/comments/{comment}', [CommentController::class, 'update']);
Route::delete('posts/comments/{comment}', [CommentController::class, 'destroy']);

Route::post('posts/{post}/like', [LikeController::class, 'like']);

Route::post('/post/create', [PostManagerController::class, 'createPost']);
Route::get('/posts', [PostManagerController::class, 'getPostsByUser']);
Route::get('/post/search', [PostManagerController::class, 'searchPost']);

Route::get('/post/update', [PostManagerController::class, 'updatePost']);
Route::delete('/post/delete', [PostManagerController::class, 'deletePosts']);

Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');

Route::middleware(['auth.users'])->group(function () {
    Route::get('/all/posts', [PostManagerController::class, 'getPosts']);
    Route::get('posts/comments', [CommentController::class, 'index']);
    Route::get('user/followers', [UserController::class, 'getFollowersByUserId']);
    Route::get('user/followings', [UserController::class, 'getFollowingsByUserId']);
});

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