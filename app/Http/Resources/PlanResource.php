<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "plan_label" => $this->name,
            "plan_fee" => $this->fee,
            "plan_validity" => $this->validity,
            "status" => $this->status,
            "created_at" => $this->created_at
        ];
    }
}
