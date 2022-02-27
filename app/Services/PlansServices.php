<?php

namespace App\Services;

use App\Models\Member\Plan;

class PlansServices
{
    public static function uniquePlan($request)
    {
        $existingPlanCount = Plan::where("fee", $request->plan_fee)->where(
            "validity",
            $request->plan_validity
        )->count();
        if ($existingPlanCount){
            return false;
        }
        return true;
    }
}
