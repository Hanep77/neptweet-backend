<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        return CommentResource::collection(Comment::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "post_id" => ["required"],
            "body" => ["required"]
        ]);

        $user_id = Auth::user()->id;
        $validated["user_id"] = $user_id;

        $comment = Comment::query()->create($validated);
        return response(new CommentResource($comment), 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::query()->find($id);

        if (!$comment) {
            throw new HttpResponseException(response([
                "message" => "not found"
            ], 404));
        }

        $validated = $request->validate([
            "body" => ["required"]
        ]);

        $comment->update($validated);
        return new CommentResource($comment);
    }

    public function delete($id)
    {
        $comment = Comment::query()->find($id);

        if (!$comment) {
            throw new HttpResponseException(response([
                "message" => "not found"
            ], 404));
        }

        $comment->delete();

        return response()->json([
            "message" => "success deleted"
        ]);
    }
}
