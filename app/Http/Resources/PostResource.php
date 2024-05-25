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
            "likes_count" => $this->likes_count,
            "is_liked_by_user" => $this->is_liked_by_user,
            "author" => [
                "id" => $this->user_id,
                "name" => $this->user->name,
                "created_at" => $this->user->created_at
            ],
            "comments" => CommentResource::collection($this->comments)
        ];
    }
}
