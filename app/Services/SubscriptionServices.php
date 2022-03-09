<?php

namespace App\Services;

use App\Models\Member\Subscription;

class SubscriptionServices
{
    /**
     * Added new subscription
     */
    public static function createSubscription($request, $account)
    {
        return Subscription::create([
            "plan_id" => $request->plan_id,
            "account_id" => $account->id,
            "start_date" => $request->plan_start_date,
            "end_date" => $request->plan_end_date,
            "charge" => $request->plan_fee,
        ]);
    }

    public static function updateSubscription(
        $request,
        Subscription $subscription
    ) {
        $subscription->fill([
            "plan_id" => $request->plan_id,
            "end_date" => $request->plan_end_date,
            "charge" => $request->plan_fee,
        ]);
        $subscription->save();
        return $subscription;
    }

    public static function queueSubscriptionChange($request)
    {
        $queuedSubscription = Subscription::updateOrCreate(
            ["account_id" => $request->account_id, "status" => 3],
            [
                "plan_id" => $request->plan_id,
                "start_date" => $request->plan_start_date,
                "end_date" => $request->plan_end_date,
                "charge" => $request->plan_fee,
                "status" => 3
            ]
        );
        return $queuedSubscription;
    }
}
