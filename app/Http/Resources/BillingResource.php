<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if ($this->status_code == 'Fully Paid'){
            $bill_due_date_in_days = 'N.A.';
        }else{
            $bill_due_date_in_days = now()->diffInDays(Carbon::parse($this->bill_due_date), false);
        }

        return [
            "id" => $this->id,
            "bill_number" => $this->bill_number,
            "status_code" => $this->status_code,
            "bill_issued_date" => $this->bill_issued_date,
            "bill_due_date" => $this->bill_due_date,
            "bill_amount" => $this->bill_amount,
            "bill_due_date_in_day" => $bill_due_date_in_days,
            "prev_due_amount" => $this->prev_due_amount,
            "billing_period" => $this->billing_period,
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'contact' => new AccountResource($this->whenLoaded('account')),
        ];
    }
}
