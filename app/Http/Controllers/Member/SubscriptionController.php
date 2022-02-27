<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use App\Models\Member\Subscription;
use App\Http\Requests\Member\StoreSubscriptionRequest;
use App\Http\Requests\Member\UpdateSubscriptionRequest;
use App\Services\Helper;
use App\Services\SubscriptionServices;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubscriptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubscriptionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubscriptionRequest  $request
     * @param  \App\Models\Member\Subscription  $subscription
     * @return SubscriptionResource
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        try {
            $subscription = SubscriptionServices::updateSubscription($request, $subscription);
            return new SubscriptionResource($subscription);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Subscription");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubscriptionRequest  $request
     * @param  \App\Models\Member\Subscription  $subscription
     * @return SubscriptionResource
     */
    public function queueSubscriptionChanges(UpdateSubscriptionRequest $request)
    {
        try {
            $subscription = SubscriptionServices::queueSubscriptionChange($request);
            return new SubscriptionResource($subscription);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Subscription");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member\Subscription  $subscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
