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
        try {
            $request->validate([
                'username' => 'required | string',
                'post' => 'required'
            ]);

            if ($request->description) {
                $desc = $request->description;
            } else {
                $desc = null;
            }

            $user = User::where('username', $request->username)->first();

            $imageName = time() . '.' . $request->post->extension();
            $request->post->move(public_path('images'), $imageName);

            $post = new Post;
            $post->user_id = $user['id'];
            $post->post = $imageName;
            $post->description = $desc;
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

    public function getPostsByUser(Request $request)
    {
        try {
            if (!$request->hasHeader('username')) {
                return response()->json(['error' => 'Username is required']);
            }
            $user = User::where('username', $request->header('username'))->first();
            if (!$user) {
                return response()->json(['error' => 'There is no user with this username']);
            } else {
                $posts = Post::where('user_id', $user->id)->paginate(5);
                if (count($posts) != 0) {
                    return response()->json([
                        "user_id" => $user['id'],
                        "posts" => PostResource::collection($posts)
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

    public function getPosts()
    {
        $posts = Post::with('user')->paginate(5);
        return response()->json([
            'success' => true,
            'message' => 'All posts retrieved successfully',
            'posts' => PostResource::collection($posts),
        ], 200);
    }

    public function searchPost(Request $request)
    {
        if ($request->hasHeader('username')) {
            $user = User::where('username', 'like', '%' . $request->header('username') . '%')->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            } else {
                $posts = Post::where('user_id', $user['id'])->get();

                return response()->json([
                    'data' => PostResource::collection($posts)
                ]);
            }
        } else {
            return response()->json(['error' => 'Search Query required'], 404);
        }
    }

    public function updatePost(Request $request)
    {
        if ($request->hasHeader('post_id') && $request->hasHeader('user_id')) {
            $post = Post::where('id', $request->header('post_id'))->where('user_id', $request->header('user_id'))->first();
            if (!$post) {
                return response()->json(['error' => 'No Post found']);
            }
            if ($request->header('description')) {
                $desc = $request->header('description');
                $post->description = $desc;
                $post->update();
                return response()->json(['message' => 'Post updated successfully!', 'data' => $post], 201);
            }
            return response()->json(['message' => 'Post did not update!', 'data' => $post], 201);
        }
        return response()->json(['error' => 'Please provide post_id and user_id in header!'], 401);
    }

    public function deletePosts(Request $request)
    {
        if (!$request->hasHeader('username')) {
            return response()->json(['error' => 'Username is required']);
        }
        $username = $request->header('username');
        $user = User::where('username', $username)->first();
        $posts = Post::where('id', $request->header('id'))->where('user_id', $user->id)->first();
        if ($posts != null) {
            $postArr = $posts->toArray();
        } else {
            return response()->json(["success" => false, 'error' => 'No posts found.']);
        }
        if (count($postArr) == 0) {
            return response()->json(['error' => 'There is no post from this user']);
        }

        $posts->delete();
        return response()->json([
            "success" => true,
            "message" => "Successfully deleted the post."
        ], 201);
    }
}