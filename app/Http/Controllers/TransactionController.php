<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Services\AccountServices;
use App\Services\BillingServices;
use App\Services\Helper;
use App\Services\TransactionServices;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
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
     * @param \App\Http\Requests\StoreTransactionRequest $request
     * @return TransactionResource
     */
    public function store(StoreTransactionRequest $request)
    {
        $transactionType = $request->transaction_type;

        DB::beginTransaction();
        try {
            if ($transactionType == 0) { //payment request
                $transaction = TransactionServices::paymentTransaction($request);
            } else { // TODO refund to be implemented
                abort(422, "Transaction not supported at the moment.");
            }
            DB::commit();
            return new TransactionResource(
                Transaction::with(["bill", "account.contact"])->find($transaction->id)
            );
        } catch (\Exception $exception) {
            DB::rollback();
            return Helper::exceptionJSON($exception, 422, "transaction");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateTransactionRequest $request
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(
        UpdateTransactionRequest $request,
        Transaction $transaction
    )
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
