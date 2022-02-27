<?php

namespace App\Services;

class ValidationRules
{
    /***
     * Returns the validation rules for contact table
     * @return string[]
     */
    public static function storeContactRules()
    {
        return [
            //validation rules for contacts table
            "first_name" => "required|string",
            "last_name" => "required|string",
            "type" => "required|string|in:customer,prospect",
            "gender" => "required|string|in:male,female",
            "dob" => "required|date_format:m/d/Y",
            "phone_number" => "required|string",
            "email" => "email|required|unique:customers,email",
            //            "email" => Rule::unique("customers")->where(function ($query) use (
            //                $phoneNumber
            //            ) {
            //                return $query->where("phone", $phoneNumber);
            //            }),
            "address" => "required|string",
            "city" => "required|string",
            "pincode" => "required|integer",
            "state" => "required|string",
        ];
    }

    public static function storeAccountRules()
    {
        return [
            //validation rules for accounts & billings
            // (pending_amount and due_date are being used in billings table as well) table
            "pending_amount" => "required|numeric",
            "due_date" => "required|date_format:m/d/Y|after_or_equal:today",
        ];
    }

    public static function storeSubscriptionRules()
    {
        return [
            //validation rules for subscriptions table
            "plan_id" => "required|integer|exists:plans,id",
            "plan_start_date" =>
                "required|date_format:m/d/Y|after_or_equal:today",
            "plan_end_date" =>
                "required|date_format:m/d/Y|after:plan_start_date",
            "plan_fee" => "required|numeric",
        ];
    }

    public static function storeBillingRules()
    {
        return [
            //validation rules for billings table
            //"billing_status_code" => "required|integer|in:0,1,2",
            // we doing it programtically in API hence billing_status_code not required from UI layer
            "payable_amount" => "required|numeric",
        ];
    }

    public static function storeTransactionRules()
    {
        return [
            //validation rules for transaction table
            "mode" => "required|integer|in:1,2,3,4,10",
            "transaction_type" => "required|integer|in:0,1",
            "payment_amount" => "required|numeric|gt:0|lte:payable_amount",
            "payable_amount" => "required|numeric|gt:0",
            "pending_amount" => "required|numeric",
            "transaction_comment" => "required|string",
        ];
    }

    public static function storePlanRules()
    {
        return [
            "plan_label" => "required|string",
            "plan_fee" => "required|numeric|gt:0",
            "plan_validity" => "required|numeric|gte:30",
        ];
    }
}
