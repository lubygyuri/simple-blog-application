<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\User\UserResource;
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
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => new UserResource($this->whenLoaded('user')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'can_edit' => $this->when(
                auth()->check(),
                fn() => auth()->user()->can('update', $this->resource)
            ),
            'can_delete' => $this->when(
                auth()->check(),
                fn() => auth()->user()->can('delete', $this->resource)
            ),
        ];
    }
}
