<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankdetailResource extends JsonResource
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
            'acc_name' => $this->acc_name,
            'acc_number' => $this->acc_number,
            'branch_name' => $this->branch_name,
            'routing_number' => $this->routing_number,
            'swift_code' => $this->swift_code,
            'cheque_url' => $this->cheque_upload ? url('uploads/investors/blank_check/' . $this->cheque_upload) : null,
            'bank' => new BankResource($this->bank),
        ];
    }
}
