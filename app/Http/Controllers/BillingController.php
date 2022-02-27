<?php

namespace App\Http\Controllers;

use App\Http\Resources\BillingResource;
use App\Models\Billing;
use App\Http\Requests\StoreBillingRequest;
use App\Http\Requests\UpdateBillingRequest;
use App\Services\BillingServices;
use App\Services\Helper;
use Illuminate\Support\Facades\Validator;
use Throwable;

class BillingController extends Controller
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
     * @param  \App\Http\Requests\StoreBillingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBillingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($id)
    {
        try {
            $bills = BillingServices::billById($id);
            if ($bills->count() === 0) {
                abort(
                    404,
                    "No bill records found for entered id " .
                    $id
                );
            }
            return BillingResource::collection($bills);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "search");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBillingRequest  $request
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBillingRequest $request, Billing $billing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Billing  $billing
     * @return \Illuminate\Http\Response
     */
    public function destroy(Billing $billing)
    {
        //
    }

    /***
     * Get bill details by account number
     */
    public function getInvoicesByAccountID($accountID)
    {
        $accountID = trim($accountID);
        $input = [
            "account_id" => $accountID,
        ];
        $rules = [
            "account_id" => "required|integer|exists:accounts,id",
        ];

        Validator::make(
            $input,
            $rules,
            $messages = [
                "required" =>
                    "The :attribute field is required for searching transactions",
                "integer" => "The :attribute field should be a valid account.",
            ]
        )->validate();
        try {
            $bills = Billing::with([
                "account.contact",
            ])->where("account_id", $accountID)->get();
            if ($bills->count() === 0) {
                abort(
                    404,
                    "There are no Bills found for selected account "
                );
            }
            return BillingResource::collection($bills);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Billing");
        }
    }
}
