<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvestorDetailsResource extends JsonResource
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
            'nid' => $this->nid,
            'nid_url' => $this->image_url,
            'user' => new UserResource($this->whenLoaded('user')),
            'bankDetails' => new BankdetailResource($this->whenLoaded('bankdetails')),
        ];
    }
}
