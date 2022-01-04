<?php

namespace App\Services;

use App\Models\Member\Account;
use App\Models\Member\Contact;
use Illuminate\Support\Facades\Validator;

class AccountServices
{
    /**
     * Generate bill number
     * @return string
     */
    public static function generateRegistrationNumber(): string
    {
        $finanicalYear = CommonServices::getFinancialYear();

        $maxRegistrationNumber = Account::where(
            "financial_year",
            $finanicalYear
        )->max("id");

        // null registration number will be returned in case of fresh install or new financial year so we start it from 0
        if (!$maxRegistrationNumber) {
            $maxRegistrationNumber = 0;
        }

        $registrationNumber = $maxRegistrationNumber + 1;
        $lengthOfRegistrationNumber = env("REGISTRATION_NUMBER_DIGITS", 6);
        $prefix = env("REGISTRATION_PREFIX", "MEM");
        return sprintf(
            $prefix . "%0" . $lengthOfRegistrationNumber . "d",
            $registrationNumber
        );
    }

    /**
     * Creates account
     * @param $request
     * @param Contact $contact
     * @return mixed
     */
    public static function createAccount($request, Contact $contact)
    {
        $finanicalYear = CommonServices::getFinancialYear();
        $registrationNumber = self::generateRegistrationNumber();
        return Account::create([
            "contact_id" => $contact->id,
            "registration_number" => $registrationNumber,
            "outstanding_payment" => $request->pending_amount,
            "due_date" => $request->due_date,
            "financial_year" => $finanicalYear,
        ]);
    }

    /***
     * Search by registration number
     * @param $registrationNumber
     * @return null
     */
    public static function customerByAccount($registrationNumber)
    {
        if ($registrationNumber) {
            return Account::with([
                "contact",
                "subscriptions",
                "bills" => function ($query) {
                    $query->latest()->first();
                },
                "transactions" => function ($query) {
                    $query->latest()->first();
                },
            ])
                ->where("registration_number", $registrationNumber)
                ->get();
        }
        return null;
    }

    /**
     * Updates account
     * @param $request
     * @param $id
     * @return mixed
     */
    public static function updateOutstandingPayment($request, $id)
    {
        $account = Account::findOrFail($id);
        $account->outstanding_payment =
            $request->payable_amount - $request->payment_amount;
        if ($account->outstanding_payment < 0)
            abort(422, "Payment amount is more than payable amount.");
        $account->save();
    }
}
