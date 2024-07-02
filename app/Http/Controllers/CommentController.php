<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(): ResourceCollection
    {
        return CommentResource::collection(Comment::all());
    }

    public function store(Request $request): CommentResource
    {
        $validated = $request->validate([
            "post_id" => ["required"],
            "body" => ["required"]
        ]);

        $user_id = Auth::user()->id;
        $validated["user_id"] = $user_id;

        $comment = Comment::query()->create($validated);
        return new CommentResource($comment);
    }

    public function update(Request $request, int $id): CommentResource | HttpResponseException
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

    public function delete(int $id): JsonResponse | HttpResponseException
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
