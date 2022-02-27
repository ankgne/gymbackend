<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Models\Member\Account;
use App\Http\Requests\Member\StoreAccountRequest;
use App\Http\Requests\Member\UpdateAccountRequest;
use App\Services\AccountServices;
use App\Services\Helper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AccountController extends Controller
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
     * @param  \App\Http\Requests\StoreAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAccountRequest  $request
     * @param  \App\Models\Member\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function destroy($id)
    {
    }

    /**
     * Mark the account as inactive
     *
     * @param $id
     * @return AccountResource
     */
    public function inactive($id)
    {
        try {
            $account = AccountServices::inactiveAccount($id);
            return new AccountResource($account);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 422, "deactivate");
        }
    }

    /**
     * Mark the account as active
     *
     * @param $id
     * @return AccountResource
     */
    public function active($id)
    {
        try {
            $account = AccountServices::activateAccount($id);
            return new AccountResource($account);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 422, "destory");
        }
    }

}
