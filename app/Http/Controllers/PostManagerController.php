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
    public function post(Request $request)
    {
        $request->validate([
            'username' => 'required | string',
            'post' => 'required'
        ]);
        // dd($request);

        $user = User::where('username', $request->username)->first();
        // dd($request->post);

        $imageName = time() . '.' . $request->post->extension();
        // dd($imageName);
        $request->post->move(public_path('images'), $imageName);

        $post = new Post;
        $post->user_id = $user['id'];
        $post->post = $imageName;
        // Post::create([
        //     'user_id' => $user['id'],
        //     'post' => $imageName,
        // ]);
        $post->save();

        return response()->json([
            "success" => true,
            "message" => "successfully post created.",
            "post" => new PostResource($post)
        ]);
    }

    public function getPosts(Request $request)
    {
        if (!$request->hasHeader('username')) {
            return  response()->json(['error' => 'Username is required']);
        }
        $user = User::where('username', $request->header('username'))->first();
        $posts = Post::where('user_id', $user['id'])->get();

        return response()->json(["posts" => $posts]);
    }

    public function deletePosts(Request $request)
    {
        if (!$request->hasHeader('username')) {
            return  response()->json(['error' => 'Username is required']);
        }
        $username = $request->header('username');
        // dd($username);
        $user = User::where('username', $username)->first();
        $posts = Post::where('user_id', $user['id'])->where('id', $request->header('id'))->first();
        // dd($posts);
        if (!$posts) {
            return response()->json(['error' => 'There is no post from this user']);
        }

        $posts->delete();
        return  response()->json([
            "success" => true,
            "message" => "Successfully deleted the post."
        ], 204);
    }
}
