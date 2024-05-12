<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "body" => $this->body,
            "created_at" => $this->created_at,
            "author" => [
                "name" => $this->user->name,
                "created_at" => $this->user->created_at,
                "body" => $this->user->body
            ],
            "comments" => $this->comments->toArray()
        ];
    }
}
