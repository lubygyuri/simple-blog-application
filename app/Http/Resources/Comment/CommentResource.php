<?php

namespace App\Http\Resources\Comment;

use App\Http\Resources\Post\PostResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->whenLoaded('user')),
            'post' => new PostResource($this->whenLoaded('post')),
            'can_delete' => $this->when(
                auth()->check(),
                fn() => auth()->user()->can('delete', $this->resource)
            ),
        ];
    }
}
