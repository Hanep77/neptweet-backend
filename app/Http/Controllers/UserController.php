<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        // validate the request
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

    public function register(Request $request)
    {
        $validated = $request->validate([
            "name" => ["required", "max:100"],
            "email" => ["required", "max:150", "unique:users,email"],
            "password" => ["required", "min:6", "max:255", "confirmed"],
        ]);

        $validated["password"] = Hash::make($validated["password"]);
        $user = User::create($validated);

        return response()->json([
            "data" => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            "message" => "success logout"
        ], 200);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
