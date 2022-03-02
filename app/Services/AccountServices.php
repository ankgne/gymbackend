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
                "subscriptions" => function ($query) {
                    $query->active()->latest();
                },
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
        if ($account->outstanding_payment < 0) {
            abort(422, "Payment amount is more than payable amount.");
        }
        $account->save();
    }

    /**
     * Returns the list of all active customers
     */
    public static function getActiveCustomers()
    {
        return Account::with([
            "contact" => function ($query) {
                $query->customer();
            },
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
            //            "bills" => function ($query) {
            //                $query->latest()->first();
            //            },
        ])
            ->active()
            ->get();
    }

    /**
     * Returns the list of all active customers
     */
    public static function getInactiveCustomers()
    {
        return Account::with([
            "contact" => function ($query) {
                $query->customer();
            },
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
            //            "bills" => function ($query) {
            //                $query->latest()->first();
            //            },
        ])
            ->inactive()
            ->get();
    }

    /**
     * Returns the list of all active customers who have outstanding payment
     */
    public static function getActiveCustomersWithUpcomingDueDate()
    {
        return Account::with([
            "contact" => function ($query) {
                $query->customer();
            },
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
            //            "bills" => function ($query) {
            //                $query->latest()->first();
            //            },
        ])
            ->where("outstanding_payment", ">", 0)
            ->where("due_date", ">", today())
            ->get();
    }

    /**
     * Returns the list of all active customers who have outstanding payment
     */
    public static function getActiveCustomersWithOverDueDate()
    {
        return Account::with([
            "contact" => function ($query) {
                $query->customer();
            },
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
        ])
            ->where("outstanding_payment", ">", 0)
            ->where("due_date", "<", today()) // due date is passed
            ->get();
    }

    /**
     * Returns the list of all active customers who have outstanding payment
     */
    public static function getSuspendedCustomers()
    {
        return Account::with([
            "contact" => function ($query) {
                $query->customer();
            },
            "subscriptions" => function ($query) {
                $query->active()->latest();
            },
//            "bills" => function ($query) {
//                $query->latest()->first();
//            },
        ])
            ->where("status", 2)
            ->get();
    }

    /**
     * Inactive account
     */
    public static function inactiveAccount($id)
    {
        $account = Account::with("contact")->findOrFail($id);
        if ($account->update(["status" => 0])) {
            return $account;
        }
        abort(422, "Failed to change status of account");
    }

    /**
     * Activate account
     */
    public static function activateAccount($id)
    {
        $account = Account::with("contact")->findOrFail($id);
        if ($account->update(["status" => 1])) {
            return $account;
        }
        abort(422, "Failed to change status of account");
        //        $account->status = 1;
        //        $account->save();
    }
}
