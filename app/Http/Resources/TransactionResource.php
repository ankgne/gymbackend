<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "receipt_number" => $this->receipt_number,
            "transaction_mode" => $this->transaction_mode,
            "transaction_type" => $this->transaction_type,
            "transaction_date" => $this->transaction_date,
            "transaction_amount" => $this->transaction_amount,
            "transaction_comment" => $this->transaction_comment,
            "due_amount_before_transaction" => $this->due_amount_before_transaction,
            "due_amount_after_transaction" => $this->due_amount_after_transaction,
            'bill' => new BillingResource($this->whenLoaded('bill')),
            'contact' => new AccountResource($this->whenLoaded('account')),
        ];
    }
}
