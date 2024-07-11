<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $posts = Post::query()->inRandomOrder()->withCount("likes")->with(["user", "comments"])->get();
        $user = Auth::user();

        $posts->each(function ($post) use ($user) {
            $post->is_liked_by_user = $post->likes->contains('user_id', $user->id);
        });

        return PostResource::collection($posts);
    }

    public function store(Request $request): PostResource
    {
        $validated = $request->validate([
            "body" => ["required"]
        ]);

        $user = Auth::user();
        $validated["user_id"] = $user->id;
        $validated["body"] = json_encode($validated["body"]);

        $post = Post::query()->create($validated);
        $post->is_liked_by_user = $post->likes->contains('user_id', Auth::user()->id);

        return new PostResource($post);
    }

    public function update(Request $request, int $id): PostResource | HttpResponseException
    {
        $post = Post::query()->withCount("likes")->with(["user", "comments"])->find($id);

        if (!$post) {
            throw new HttpResponseException(response([
                "message" => "not found"
            ], 401));
        }

        $validated = $request->validate([
            "body" => ["required"]
        ]);

        $post->update($validated);

        return new PostResource($post);
    }

    public function show(int $id): PostResource | HttpResponseException
    {
        $post = Post::query()->withCount("likes")->with(["user", "comments"])->find($id);
        $post->is_liked_by_user = $post->likes->contains('user_id', Auth::user()->id);

        if (!$post) {
            throw new HttpResponseException(response([
                "message" => "not found"
            ], 401));
        }

        return new PostResource($post);
    }
    public function delete(int $id): JsonResponse | HttpResponseException
    {
        $post = Post::query()->find($id);

        if (!$post) {
            throw new HttpResponseException(response([
                "message" => "not found"
            ], 401));
        }

        $post->delete();

        return response()->json([
            "message" => "success deleted"
        ]);
    }
}
