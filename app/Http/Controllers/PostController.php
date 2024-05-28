<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::query()->inRandomOrder()->withCount("likes")->with(["user", "comments"])->paginate(perPage: 30);
        $user = Auth::user();

        $posts->each(function ($post) use ($user) {
            $post->is_liked_by_user = $post->likes->contains('user_id', $user->id);
        });

        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "body" => ["required"]
        ]);

        $user = Auth::user();
        $validated["user_id"] = $user->id;

        $post = Post::query()->create($validated);
        $post->is_liked_by_user = $post->likes->contains('user_id', Auth::user()->id);

        return response(new PostResource($post), 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::query()->find($id);

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

    public function show($id)
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
    public function delete($id)
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
