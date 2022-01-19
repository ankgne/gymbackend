<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Services\Helper;
use App\Services\TransactionServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
            if ($transactionType == 0) {
                //payment request
                $transaction = TransactionServices::paymentTransaction(
                    $request
                );
            } else {
                // TODO refund to be implemented
                abort(422, "Transaction not supported at the moment.");
            }
            DB::commit();
            return new TransactionResource(
                Transaction::with(["bill", "account.contact"])->find(
                    $transaction->id
                )
            );
        } catch (\Exception $exception) {
            DB::rollback();
            return Helper::exceptionJSON($exception, 422, "transaction");
        }
    }

    /***
     * Get traction details by account number
     */
    public function getTransactionsByAccountID($accountID)
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
            $transactions = Transaction::with([
                "bill",
                "account.contact",
            ])->where("account_id", $accountID)->get();
            if ($transactions->count() === 0) {
                abort(
                    404,
                    "There are no transactions found for selected account "
                );
            }
            return TransactionResource::collection($transactions);
        } catch (\Exception $exception) {
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
    ) {
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
