<?php

namespace App\Actions\Billing;

use App\Models\Billing;
use App\Models\Member\Account;
use App\Models\Member\Subscription;
use App\Services\BillingServices;
use App\Services\CommonServices;
use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GenerateInvoice
{
    public static $commandObject = null;

    /**
     * Get all active subscriptions of active accounts only
     * @return mixed
     */
    public static function getActiveSubscriptionsWithAccountDetails(
        $billGenerationDate = null
    ) {
        if (!$billGenerationDate) {
            $searchDate = Helper::todaysDateString();
        } else {
            $searchDate = Carbon::parse($billGenerationDate)->format("Y-m-d");
        }

        return Subscription::with("account")
            ->whereHas("account", function (Builder $query) {
                $query->active();
            })
            ->active()
            ->where("end_date", $searchDate)
            ->get();
    }

    /**
     * Creates bill. Note we are passing updatedSubscription and that's because we want to have next subscriptions bill
     * as we are taking advance fees from members
     * @param $subscription
     * @param $updatedSubscription
     * @return mixed
     */
    public static function createBillingEntry(
        $subscription,
        $updatedSubscription
    ) {
        $todaysDate = Carbon::now();
        $todaysDateString = $todaysDate->toDateString();
        // bill due date 7 days from bill generation date
        $billDueDate = $todaysDate->addDays(7);
        $billDueDateString = $billDueDate->toDateString();

        $billNumber = BillingServices::generateBillNumber();
        $finanicalYear = CommonServices::getFinancialYear();

        //account related details
        $previousOutstandingAmount =
            $subscription["account"]["outstanding_payment"];
        $accountID = $subscription["account_id"];

        //plan related details
        $planFees = $updatedSubscription["plan"]["fee"];
        $planID = $updatedSubscription["plan_id"];

        $billingPeriod =
            $updatedSubscription->start_date .
            "-" .
            $updatedSubscription->end_date;

        self::$commandObject->info("Bill entry created");

        return Billing::create([
            "bill_number" => $billNumber,
            "account_id" => $accountID,
            "status_code" => 0, //unpaid status code
            "bill_issued_date" => $todaysDateString,
            "bill_due_date" => $billDueDateString,
            "bill_amount" => $planFees + $previousOutstandingAmount, // total amount
            "prev_due_amount" => $previousOutstandingAmount, // from account table
            "plan_id" => $planID, // from subscription table
            "financial_year" => $finanicalYear,
            "billing_period" => $billingPeriod,
        ]);
    }

    /**
     * Updates outstanding amount in accounts table
     * @param $request
     * @param $id
     * @return mixed
     */
    public static function updateOutstandingPayment($bill)
    {
        $account = Account::findOrFail($bill->account_id);

        self::$commandObject->info(
            "Outstanding amount before update " . $account->outstanding_payment
        );

        $account->outstanding_payment = $bill->bill_amount;

        $account->save();

        self::$commandObject->info(
            "Outstanding amount after update " . $account->outstanding_payment
        );

        self::$commandObject->info(
            "Outstanding Payment updated in accounts table"
        );
    }

    /**
     * Extends subscription by default based on the existing plan
     * @param $subscription
     * @return mixed
     */
    public static function extendSubscriptionEndDate($subscription)
    {
        $subscription = Subscription::findOrFail($subscription->id);
        $subscriptionValidity = $subscription->plan->validity;

        $newStartDate = Carbon::parse($subscription->end_date)->addDays(1);
        $subscription->start_date = $newStartDate;

        $newEndDate = Carbon::parse($subscription->end_date)->addDays(
            $subscriptionValidity
        );
        $subscription->end_date = $newEndDate;

        $subscription->save();

        self::$commandObject->info(
            "Subscription dates extended in subscriptions table"
        );

        return $subscription;
    }

    /**
     * Generate Bill - by defaults it picks today's date for bill generation but can be passed specific date
     * if the batch fails for a specific day
     */
    public static function generateBill($object, $billGenerationDate = null)
    {
        self::$commandObject = $object;
        $todaysDate = Carbon::now();
        self::$commandObject->newLine();
        if ($billGenerationDate) {
            self::$commandObject->info(
                "Bill Generation started for " . $billGenerationDate
            );
        } else {
            self::$commandObject->info(
                "Bill Generation started for " . $todaysDate->format("m/d/Y")
            );
        }

        // get all active subscriptions with associated account details with end date equal to today's date or passed date
        $subscriptions = self::getActiveSubscriptionsWithAccountDetails(
            $billGenerationDate
        );

        self::$commandObject->newLine();
        self::$commandObject->info(
            "Count of members (subscriptions) for which bill would be generated " .
                $subscriptions->count()
        );

        // loop through all the active subscriptions
        foreach ($subscriptions as $subscription) {
            self::$commandObject->info("---------------------");
            self::$commandObject->info(
                "Running processing for membership number " .
                    $subscription["account"]["registration_number"]
            );
            DB::beginTransaction();
            try {
                // Check if there is any queued subscription available for update for the account
                // if there is one then update the make the queued subscription as active and ongoing as inactive
                // and use new subscription for bill generation
                $newSubscriptionEnabled = self::isQueuedSubscriptionImplemented(
                    $subscription
                );

                // if no queued subscription found then extend the existing subscription else use the updated existing subscription
                if (!$newSubscriptionEnabled) {
                    $updatedSubscription = self::extendSubscriptionEndDate(
                        $subscription
                    );
                } else {
                    $updatedSubscription = $newSubscriptionEnabled;
                }

                //create new bill
                $bill = self::createBillingEntry(
                    $subscription,
                    $updatedSubscription
                );

                //update outstanding amount in account's table with new billed amount
                self::updateOutstandingPayment($bill);

                self::$commandObject->newLine(1);
                DB::commit();
            } catch (\Exception $exception) {
                DB::rollback();
                self::$commandObject->error(
                    "Processing failed for membership number " .
                        $subscription["account"]
                );
                self::$commandObject->error("Exception Details " . $exception);
                // TODO send email on failure
            }
            self::$commandObject->info("---------------------");
        }
        self::$commandObject->newLine();
        self::$commandObject->info("Exiting from bill generation process");
    }

    /***
     *Check for queued subscription to be activated for account and if there is one then activate it
     */
    public static function isQueuedSubscriptionImplemented($subscription)
    {
        self::$commandObject->info(
            "Checking for queued subscriptions for " .
                $subscription["account"]["registration_number"]
        );

        $account = $subscription->account_id;
        $queuedSubscription = Subscription::where("account_id", $account)
            ->queue()
            ->latest()
            ->first();
        if ($queuedSubscription) {
            self::$commandObject->info(
                "Queued subscription found for changing plan from " .
                    $subscription["plan"]["name"] .
                    " to " .
                    $queuedSubscription->plan->name .
                    " for " .
                    $subscription["account"]["registration_number"]
            );
            self::$commandObject->info(
                "Updating ongoing subscription with queued subscription details for " .
                    $subscription["account"]["registration_number"]
            );
            //get the active , latest and first subscription.
            // As of now, customer can have one active subscription at a time but then also we are picking latest and first
            $ongoingSubscription = Subscription::where("account_id", $account)
                ->active()
                ->latest()
                ->first();

            $ongoingSubscription->status = 4;
            //            $ongoingSubscription->plan_id = $queuedSubscription->plan_id;
            //            $ongoingSubscription->start_date = $queuedSubscription->start_date;
            //            $ongoingSubscription->end_date = $queuedSubscription->end_date;
            //            $ongoingSubscription->charge = $queuedSubscription->charge;
            $ongoingSubscription->save();
            //make the queue subscription as active
            $queuedSubscription->status = 1;
            $queuedSubscription->save();
            self::$commandObject->info(
                "Update done for " .
                    $subscription["account"]["registration_number"] .
                    "and new plan is " .
                    $queuedSubscription->plan->name
            );
            return $queuedSubscription;
        }

        self::$commandObject->info(
            "Queued subscription not found for " .
                $subscription["account"]["registration_number"]
        );
        return false;
    }
}
