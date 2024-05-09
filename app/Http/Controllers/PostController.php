<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input("page");
        $posts = Post::query()->paginate(perPage: 20, page: $page);
        return PostResource::collection($posts);
    }
}
