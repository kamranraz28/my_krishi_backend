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
            'acc_name' => $this->acc_name,
            'acc_number' => $this->acc_number,
            'branch_name' => $this->branch_name,
            'routing_number' => $this->routing_number,
            'swift_code' => $this->swift_code,
            'check_url' => $this->check_upload ? url('uploads/investors/blank_check/' . $this->check_upload) : null,
            'bank' => $this->whenLoaded('bank', function () {
                return [
                    'id' => $this->bank->id,
                    'name' => $this->bank->name,
                ];
            }),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
