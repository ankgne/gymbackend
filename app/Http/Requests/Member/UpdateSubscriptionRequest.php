<?php

namespace App\Http\Requests\Member;

use App\Services\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
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
        // plan start date could be before today's date as we are not allowing on UI to edit start date of ongoing plan
        $additionalSubscriptionRules = [
            "plan_start_date" =>
                "required|date_format:m/d/Y",
            "account_id" => "required|integer|exists:accounts,id"
        ];

        return array_merge(
            ValidationRules::storeSubscriptionRules(),
            $additionalSubscriptionRules
        );
    }
}
