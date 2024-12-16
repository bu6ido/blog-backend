<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 
            'content' => $this->content, 
            'post_id' => $this->post_id, 
            'post' => new PostResource($this->whenLoaded('post')),            
            'user_id' => $this->user_id, 
            'user' => new UserResource($this->whenLoaded('user')),            
        ];
    }
}
