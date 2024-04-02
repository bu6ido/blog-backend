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
        $viewType = $request->input('view_type');
        $isShowView = !empty($viewType) && ($viewType === 'show');
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->when($isShowView, $this->content),
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')), 
            'comments_count' => $this->whenCounted('comments'), 
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ];
    }
}

