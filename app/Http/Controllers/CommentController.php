<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::all();
        return response()->json($comments);
    }

    public function show(Comment $comment)
    {
        return response()->json($comment);
    }

    public function store(Post $post, Request $request)
    {
        // Validate request
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Create comment
        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->user_id = $post->user_id; // Assuming authentication is implemented
        $comment->content = $request->input('content');
        $comment->save();

        return response()->json(['message' => 'Comment added successfully'], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:255',
            ]);

            $comment->content = $request->input('content');
            $comment->save();
            return response()->json(['message' => 'Comment updated successfully']);
        } catch (ValidationException $e) {
            $error = $e->validator->errors();
            return response()->json(['error' => $error]);
        }
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }
}