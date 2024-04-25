<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Post $post, Request $request)
    {
        $like_user = User::where('username', $request->username)->first();
        if (!$like_user) {
            return response()->json(['message' => 'Username is required to like posts.']);
        }
        // Check if the user has already liked the post
        $existingLike = Like::where('post_id', $post->id)
            ->where('user_id', $post->user_id)
            ->first();
        if ($existingLike) {
            if ($existingLike->like_user_id === $like_user->id) {
                $existingLike->delete();
                $likeCount = Like::where('post_id', $post->id)->count();
                $data = ['total_likes' => $likeCount, 'message' => 'You have unlike the post'];
            } else {
                $like = new Like();
                $like->post_id = $post->id;
                $like->user_id = $post->user_id;
                $like->like_user_id = $like_user->id;
                $like->save();

                $likeCount = Like::where('post_id', $post->id)->count();
                $data = [
                    'total_likes' => $likeCount,
                    'message' => 'Post liked successfully'
                ];
            }
            return response()->json(['data' => $data], 400);
        }

        // Create like
        $like = new Like();
        $like->post_id = $post->id;
        $like->user_id = $post->user_id;
        $like->like_user_id = $like_user->id;
        $like->save();

        $likeCount = Like::where('post_id', $post->id)->count();
        $data = [
            'total_likes' => $likeCount,
            'message' => 'Post liked successfully'
        ];
        return response()->json(['data' => $data]);
    }
}