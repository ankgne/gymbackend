<?php

namespace App\Http\Resources;

use App\Models\Attendance;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->resource->relationLoaded("contact")) {
            return [
                "id" => $this->contact->id,
                "name" => $this->contact->name,
                "type" => $this->contact->type,
                "gender" => $this->contact->gender,
                "dob" => $this->contact->dob,
                "phone_number" => $this->contact->phone,
                "email" => $this->contact->email,
                "address" => $this->contact->address,
                "city" => $this->contact->city,
                "state" => $this->contact->state,
                "pincode" => $this->contact->pincode,
                "created_by" => $this->contact->created_by,
                "account" => $this->accountArray(),
            ];
        } else {
            return
                $this->accountArray();
        }
    }

    private function accountArray()
    {
        return [
            "id" => $this->id,
            "account_id" => $this->registration_number,
            "status" => $this->status,
            "outstanding_payment" => $this->outstanding_payment,
            "due_date" => $this->due_date,
            "created_on" => $this->created_at,
            "subscriptions" => SubscriptionResource::collection(
                $this->whenLoaded("subscriptions")
            ),
            "bills" => BillingResource::collection(
                $this->whenLoaded("bills")
            ),
            "transactions" => TransactionResource::collection(
                $this->whenLoaded("transactions")
            ),
            "attendances" => AttendanceResource::collection(
                $this->whenLoaded("attendances")
            ),
        ];
    }
}
