<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'id' =>$this->id,
            'project_id' => $this->project_id,
            'total_unit' => $this->total_unit,
            'status' => $this->status,
            'Booking_date' => $this->created_at,
            'project' => new ProjectResource($this->whenLoaded('project')),
        ];
    }
}
