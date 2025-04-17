<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailsResource extends JsonResource
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
            'title' => $this->title,
            'total_price' => $this->total_price,
            'unit_price' => $this->unit_price,
            'unit' => $this->unit,
            'booked_unit' => $this->booked_unit,
            'remaining_unit' => $this->unit - $this->booked_unit,
            'location' => $this->location,
            'location_map' => $this->location_map,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'youtube_video' => $this->youtube_video,
            'duration' => $this->duration,
            'return_amount' => $this->return_amount,
            'closing_amount' => $this->closing_amount,
            'service_charge' => $this->service_charge,
        ];
    }
}
