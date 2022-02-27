<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Member\Account;
use Carbon\Carbon;

class BillingServices
{
    /**
     * Generate bill number
     * @return string
     */
    public static function generateBillNumber(): string
    {
        $finanicalYear = CommonServices::getFinancialYear();

        $maxBillingNumber = Billing::where(
            "financial_year",
            $finanicalYear
        )->max("id");

        // null billing number will be returned in case of fresh install or new financial year so we start from 0
        if (!$maxBillingNumber) {
            $maxBillingNumber = 0;
        }

        $billingNumber = $maxBillingNumber + 1;
        $lengthOfBillNumber = env("BILL_NUMBER_DIGITS", 6);
        $prefix = env("BILL_PREFIX", "BILL");
        return sprintf(
            $prefix . "%0" . $lengthOfBillNumber . "d",
            $billingNumber
        );
    }

    /**
     * Creates billing entry in database when a new customer is registered
     * @param $request
     * @param Account $account
     * @return mixed
     */
    public static function createUIBillingEntry($request, $accountID)
    {
        $todaysDate = Carbon::now()->toDateString();
        $billNumber = self::generateBillNumber();
        $finanicalYear = CommonServices::getFinancialYear();

        $billingStatusCode = self::getBillingStatusCode(
            $request->payable_amount,
            $request->payment_amount
        );

        $billingPeriod = $request->plan_start_date . "-" . $request->plan_end_date;

        return Billing::create([
            "bill_number" => $billNumber,
            "account_id" => $accountID,
            "status_code" => $billingStatusCode,
            "bill_issued_date" => $todaysDate,
            "bill_due_date" => $request->due_date,
            "bill_amount" => $request->payable_amount,
            "prev_due_amount" => 0, // new registration there will be zero outstanding
            "plan_id" => $request->plan_id, // plan id from request
            "financial_year" => $finanicalYear,
            "billing_period" => $billingPeriod //billing period (based on subscription start and end date) in billing table
        ]);
    }

    /**
     * Update billing code status for passed bill ID
     * @param $request
     * @param $id
     */
    public static function updateBillingCode($request, $id)
    {
        $billing = Billing::findOrFail($id);

        if ($request->payment_amount == 0) {
            // do not update billing table if there is no payment being made and leave the entry as is
            return;
        }

        $billingStatusCode = self::getBillingStatusCode(
            $request->payable_amount,
            $request->payment_amount
        );

        $billing->status_code = $billingStatusCode;
        $billing->save();
    }

    /**
     * Determines billing status code based on outstanding amount and payment being made
     * @param $payableAmount
     * @param $paymentAmount
     * @return int
     * 0 unpaid (default)
     * 1 fully paid
     * 2 partial paid
     */
    public static function getBillingStatusCode($payableAmount, $paymentAmount)
    {
        $outstandingAmount = $payableAmount - $paymentAmount;
        if ($outstandingAmount == 0) {
            return 1; //fully paid
        } elseif ($outstandingAmount > 0 && $paymentAmount > 0) {
            return 2; // partial paid
        } elseif ($outstandingAmount < 0) {
            abort(422, "Payment amount is more than payable amount.");
        } else {
            return 0; // default unpaid
        }
    } // generate an exception as this is case when more account is being paid than owed

    /***
     * Gets the bill by it's ID
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public static function billById($id)
    {
        if ($id) {
            //gets the plan ID associated with the bill generated
            // note member can change its plan hence always plan ID associated with bill
            return Billing::with(["plan", "account.contact"])
                ->where("id", $id)
                ->get();
        }
        return null;
    }
}
