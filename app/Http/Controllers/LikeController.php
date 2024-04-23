<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function like(Post $post, Request $request)
    {
        // Check if the user has already liked the post
        $existingLike = Like::where('post_id', $post->id)
            ->where('user_id', $post->user_id) // Assuming authentication is implemented
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'You have unlike the post'], 400);
        }

        // Create like
        $like = new Like();
        $like->post_id = $post->id;
        $like->user_id = $post->user_id; // Assuming authentication is implemented
        $like->save();

        return response()->json(['message' => 'Post liked successfully'], 201);
    }
}
