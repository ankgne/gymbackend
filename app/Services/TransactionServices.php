<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\Member\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionServices
{
    /**
     * Generate bill number
     * @return string
     */
    public static function generateReceiptNumber(): string
    {
        $prefix = env("RECEIPT_PREFIX", "RCT");
        $finanicalYear = CommonServices::getFinancialYear();

        $latestTransaction = Transaction::where(
            "financial_year",
            $finanicalYear
        )
            ->latest()
            ->first();

        // null receipt number will be returned in case of fresh install or new financial year so we start it from 0
        if (!$latestTransaction) {
            $maxReceiptNumber = 0;
        }else{
            // picking second half of the array as the format of receipt number is prefix+number
            // casting it to int as well
            $latestReceipt = $latestTransaction->receipt_number;
            $maxReceiptNumber = (int)explode($prefix, $latestReceipt, 2)[1];
        }

        $receiptNumber = $maxReceiptNumber + 1;
        $lengthOfReceiptNumber = env("RECEIPT_NUMBER_DIGITS", 6);

        return sprintf(
            $prefix . "%0" . $lengthOfReceiptNumber . "d",
            $receiptNumber
        );
    }

    /**
     * Logs transactions entry at the time of member registration and for payments from Admin UI
     * @param $request
     * @param Account $account
     * @param Billing $bill
     * @return mixed
     */
    public static function logUITransaction(
        $request,
        $accountID,
        $billID,
        $paymentRequest = false
    ) {
        $todaysDate = Carbon::now()->toDateString();
        $receiptNumber = self::generateReceiptNumber();
        $finanicalYear = CommonServices::getFinancialYear();

        if ($paymentRequest) {
            // request coming for making a payment
            $transactionComment = "Payment received by " . Auth::user()->name;
        } else {
            $transactionComment = $request->transaction_comment;
        }

        return Transaction::create([
            "receipt_number" => $receiptNumber,
            "account_id" => $accountID,
            "bill_id" => $billID,
            "transaction_mode" => $request->mode,
            "transaction_type" => $request->transaction_type,
            "transaction_date" => $todaysDate,
            "transaction_amount" => $request->payment_amount,
            "transaction_comment" => $transactionComment,
            "financial_year" => $finanicalYear,
        ]);
    }

    public static function paymentTransaction($request)
    {
        $accountID = $request->account_id;
        $billID = $request->bill_id;
        $transaction = TransactionServices::logUITransaction(
            $request->toArray(),
            $accountID,
            $billID,
            true
        );
        AccountServices::updateOutstandingPayment($request, $accountID);
        BillingServices::updateBillingCode($request, $billID);

        return $transaction;
    }
}
