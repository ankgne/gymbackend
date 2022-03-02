<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            "account_id" => $this->account_id,
            "attendance_date" => $this->attendance_date,
            "in_time" => $this->in_time,
            "out_time" => $this->out_time,
            "duration" => $this->duration,
            'contact' => new AccountResource($this->whenLoaded('account')),
        ];
    }
}
