<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_all(): void
    {
        $userCreated = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            "accept" => "application/json"
        ]);

        $token = $userCreated->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $response = $this->get('/api/posts', $headers);
        $response->assertStatus(200);
        // Log::info($response->json());
    }

    public function test_create()
    {
        $userCreated = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            "accept" => "application/json"
        ]);

        $token = $userCreated->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $this->post('/api/posts/create', [
            "body" => "sebut saja ini caption"
        ], $headers)->assertStatus(201);
    }

    public function test_update()
    {
        $userCreated = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            "accept" => "application/json"
        ]);

        $token = $userCreated->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $response = $this->put("/api/posts/update/2", [
            "body" => "post updated"
        ], $headers);

        $response->assertStatus(200)->assertJson(["data" => ["body" => "post updated"]]);
    }

    public function test_get_post()
    {
        $userCreated = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            "accept" => "application/json"
        ]);

        $token = $userCreated->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $response = $this->get("/api/posts/2", $headers);

        $response->assertStatus(200);
    }

    public function test_delete()
    {
        $userCreated = $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ], [
            "accept" => "application/json"
        ]);

        $token = $userCreated->json('token');
        $headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        $response = $this->delete("/api/posts/delete/2", $headers);

        $response->assertStatus(200)->assertJson(["message" => "success deleted"]);
    }
}
