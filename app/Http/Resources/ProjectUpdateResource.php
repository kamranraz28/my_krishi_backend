<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectUpdateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'description' => $this->description,
            'image_urls' => $this->image_urls,
            'user' => new UserResource($this->user),
            'comments' => CommentResource::collection($this->whenLoaded('comment')),
            'created_at' => $this->created_at,
        ];
    }
}
