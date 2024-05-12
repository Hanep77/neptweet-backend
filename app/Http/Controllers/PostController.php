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
        $posts = Post::query()->inRandomOrder()->with(["user", "comments"])->paginate(perPage: 30);
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
        $post = Post::query()->find($id);

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
