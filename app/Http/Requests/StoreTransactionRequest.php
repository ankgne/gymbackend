<?php

namespace App\Http\Requests;

use App\Services\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
        $additionalTransactionRules = [
            "account_id" => "required|integer|exists:accounts,id",
            "bill_id" => "required|integer|exists:billings,id",
        ];

        $transactionRules = array_merge(
            ValidationRules::storeTransactionRules(),
            ValidationRules::storeBillingRules(),
            $additionalTransactionRules
        );
        // remove the rule of transaction comments as we will be setting it programmatically when saving transaction
        unset($transactionRules["transaction_comment"]);

        return $transactionRules;
    }
}
