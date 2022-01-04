<?php

namespace App\Http\Requests\Member;

use App\Services\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO making it true as we will be protecting the routes with ADMIN and Owner middlewares
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::storeContactRules(),
            ValidationRules::storeAccountRules(),
            ValidationRules::storeSubscriptionRules(),
            ValidationRules::storeBillingRules(),
            ValidationRules::storeTransactionRules()
        );
    }
}
