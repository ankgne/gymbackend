<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "type" => $this->type,
            "gender" => $this->gender,
            "dob" => $this->dob,
            "phone_number" => $this->phone,
            "email" => $this->email,
            "address" => $this->address,
            "city" => $this->city,
            "state" => $this->state,
            "pincode" => $this->pincode,
            "created_by" => $this->created_by,
            'account' => new AccountResource($this->whenLoaded('account'))
        ];
    }
}
