<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'unique_id' => $this->unique_id,
            'status' => $this->status,

            //new: Use when you're dealing with one-to-one relationships (hasOne, belongsTo).
            'details' => new DetailsResource($this->whenLoaded('details')),

            'faqs' => FaqResource::collection($this->whenLoaded('faq')),

        ];
    }
}
