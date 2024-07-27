<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request): JsonResponse | HttpResponseException
    {
        $credentials = $request->validate([
            "email" => ["required"],
            "password" => ["required"]
        ]);

        if (!Auth::attempt($credentials)) {
            throw new HttpResponseException(response([
                "error" => [
                    "message" => "Email or password incorrect"
                ]
            ], 401));
        }

        $user = Auth::user();
        $token = $request->user()->createToken("main");
        return response()->json([
            "data" => $user,
            'token' => $token->plainTextToken
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "name" => ["required", "max:100"],
            "email" => ["required", "max:150", "unique:users,email"],
            "password" => ["required", "min:6", "max:255", "confirmed"],
        ]);

        $validated["password"] = Hash::make($validated["password"]);
        $user = User::query()->create($validated);

        return response()->json([
            "data" => $user
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json([
            "message" => "success logout"
        ], 200);
    }

    public function search(Request $request): ResourceCollection | HttpResponseException
    {
        $query = join(" ", explode('+', $request->input('query')));
        $query = preg_replace('/\s+/', ' ', $query);
        $users = User::query()->where('name', 'LIKE', "%$query%")->get();
        if (!count($users)) {
            throw new HttpResponseException(response([
                "error" => [
                    "message" => "Not Found"
                ]
            ], 404));
        }
        return UserResource::collection($users);
    }

    public function show(int $id): UserResource | HttpResponseException
    {
        $user = User::query()->with(['posts' => function ($query) {
            $query->withCount('likes');
            $query->orderBy('created_at', 'desc');
        }])->find($id);

        $user->posts->each(function ($post) {
            $post->is_liked_by_user = $post->likes->contains('user_id', Auth::user()->id);
        });


        if (!$user) {
            throw new HttpResponseException(response([
                "error" => [
                    "message" => "Not Found"
                ]
            ], 404));
        }

        return new UserResource($user);
    }

    public function me(Request $request): UserResource
    {
        $user = $request->user()->load(['posts' => function ($query) {
            $query->withCount('likes');
        }]);

        $user->posts->each(function ($post) {
            $post->is_liked_by_user = $post->likes->contains('user_id', Auth::user()->id);
        });

        return new UserResource($user);
    }
}
