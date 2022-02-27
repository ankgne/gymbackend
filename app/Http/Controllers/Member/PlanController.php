<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Member\Plan;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Services\Helper;
use App\Services\PlansServices;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $plans = Plan::all();
            if ($plans->count() === 0) {
                abort(404, "No plans found");
            }
            return PlanResource::collection($plans);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Plan");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StorePlanRequest $request
     * @return PlanResource
     */
    public function store(StorePlanRequest $request)
    {
        try {
            if (!PlansServices::uniquePlan($request)) {
                abort(
                    422,
                    "There is an existing plan with same validity and fees available . Please check the list of plans"
                );
            }
            $plan = Plan::create([
                "name" => $request->plan_label,
                "fee" => $request->plan_fee,
                "validity" => $request->plan_validity,
                "status" => 1,
            ]);
            return new PlanResource($plan);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Plan");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Member\Plan $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePlanRequest $request
     * @param \App\Models\Member\Plan $plan
     * @return PlanResource
     */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        try {
            $plan->status = $request->status;
            $plan->save();
            return new PlanResource($plan);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Plan");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Member\Plan $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        //
    }

    public function getActivePlans()
    {
        try {
            $plans = Plan::active()->get();
            if ($plans->count() === 0) {
                abort(404, "No active plans found");
            }
            return PlanResource::collection($plans);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Plan");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePlanRequest $request
     * @param \App\Models\Member\Plan $plan
     * @return PlanResource
     */
    public function activatePlan(Plan $plan)
    {
        try {
            $plan->status = 1;
            $plan->save();
            return new PlanResource($plan);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Plan");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdatePlanRequest $request
     * @param \App\Models\Member\Plan $plan
     * @return PlanResource
     */
    public function deactivatePlan(Plan $plan)
    {
        try {
            $plan->status = 0;
            $plan->save();
            return new PlanResource($plan);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Plan");
        }
    }
}
