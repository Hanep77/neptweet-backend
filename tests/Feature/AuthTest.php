<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterSuccess()
    {
        $this->post('/api/register', [
            "name" => "anjay",
            "email" => "anjay@gmail.com",
            "password" => "rahasia",
            "password_confirmation" => "rahasia"
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "name" => "anjay",
                    "email" => "anjay@gmail.com",
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post('/api/register', [
            "name" => "",
            "email" => "yudis@gmail.com",
            "password" => "rahasia"
        ], [
            "accept" => "application/json"
        ])
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);


        $this->post('/api/login', [
            "email" => "test@example.com",
            "password" => "password"
        ])
            ->assertStatus(200)
            ->assertJson([
                "data" => $user->toArray()
            ]);
    }

    public function testLoginFailed()
    {
        $this->seed(UserSeeder::class);

        $this->post('/api/login', [
            "email" => "yudis123@gmail.com",
            "password" => "rahasia"
        ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => "Email or password incorrect"
                ]
            ]);
    }

    public function testLogout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            "accept" => "application/json"
        ]);

        $token = $response->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token];

        $this->post('/api/logout', [], $headers)
            ->assertStatus(200)
            ->assertJson([
                "message" => "success logout"
            ]);
    }
}
