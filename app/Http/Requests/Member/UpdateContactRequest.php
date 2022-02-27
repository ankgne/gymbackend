<?php

namespace App\Http\Requests\Member;

use App\Services\ValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
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
        $additionalContactRules = [
            "email" => [
                "required",
                "email",
                Rule::unique("customers","email")->ignore($this->contact->id),
            ],
        ];
        $contactRules = array_merge(
            ValidationRules::storeContactRules(),
            $additionalContactRules
        );
        return $contactRules;
    }
}
