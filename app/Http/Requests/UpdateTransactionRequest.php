<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "mode" => "required|integer|in:0,1,2,3,10",
            "transaction_type" => "required|integer|in:0,1",
            "payment_amount" => "required|numeric",
            "transaction_comment" => "required|string",
        ];
    }
}
