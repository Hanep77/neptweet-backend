<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    private function isNotFound($post): void
    {
        if (!$post) {
            throw new HttpResponseException(response([
                "not found"
            ], 404));
        }
    }

    public function store(int $id): JsonResponse
    {
        $post = Post::query()->find($id);
        $user = Auth::user();
        $this->isNotFound($post);
        Log::info($user);

        $like = Like::query()->create([
            "user_id" => $user->id,
            "post_id" => $id
        ]);

        if ($like) {
            return response()->json([
                "message" => "liked successfully"
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $post = Post::query()->find($id);
        $user = Auth::user();
        $this->isNotFound($post);
        $like = $post->likes()->where("user_id", $user->id);

        if ($like->delete()) {
            return response()->json([
                "message" => "unliked successfully"
            ]);
        }
    }
}
