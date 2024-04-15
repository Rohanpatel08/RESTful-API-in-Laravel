<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class PostManagerController extends Controller
{
    public function createPost(Request $request)
    {
        $request->validate([
            'username' => 'required | string',
            'post' => 'required'
        ]);

        try {

            $user = User::where('username', $request->username)->first();

            $imageName = time() . '.' . $request->post->extension();
            $request->post->move(public_path('images'), $imageName);

            $post = new Post;
            $post->user_id = $user['id'];
            $post->post = $imageName;
            $post->save();

            return response()->json([
                "success" => true,
                "message" => "successfully post created.",
                "post" => new PostResource($post)
            ]);
        } catch (Exception $e) {
            return response()->json([
                "message" => "Something went wrong"
            ]);
        }
    }

    public function getPosts(Request $request)
    {
        try {
            if (!$request->hasHeader('username')) {
                return response()->json(['error' => 'Username is required']);
            }
            $user = User::where('username', $request->header('username'))->first();
            if (!$user) {
                return response()->json(['error' => 'There is no user with this username']);
            } else {
                $posts = Post::where('user_id', $user['id'])->get();
                if (count($posts) != 0) {
                    return response()->json([
                        "user_id" => $user['id'],
                        "posts" => $posts
                    ]);
                } else {
                    return response()->json(['message' => 'There is no posts posted by this user.']);

                }
            }


        } catch (Exception $e) {
            return response()->json([
                "message" => "Something went wrong"
            ]);
        }
    }

    public function deletePosts(Request $request)
    {
        if (!$request->hasHeader('username')) {
            return response()->json(['error' => 'Username is required']);
        }
        $username = $request->header('username');
        $user = User::where('username', $username)->first();
        $posts = Post::where('user_id', $user['id'])->where('id', $request->header('id'))->first();
        if (count($posts) == 0) {
            return response()->json(['error' => 'There is no post from this user']);
        }

        $posts->delete();
        return response()->json([
            "success" => true,
            "message" => "Successfully deleted the post."
        ], 204);
    }
}