<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\MemberListResource;
use App\Jobs\ProcessAdditionalRegistrationSteps;
use App\Mail\MemberRegistered;
use App\Models\Member\Account;
use App\Models\Member\Member;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\Member\Subscription;
use App\Services\AccountServices;
use App\Services\BillingServices;
use App\Services\ContactServices;
use App\Services\EmailServices;
use App\Services\Helper;
use App\Services\SubscriptionServices;
use App\Services\TransactionServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try {
            $members = ContactServices::getCustomers();
            if ($members->count() === 0) {
                abort(404, "There are no members found");
            }
            return ContactResource::collection($members);
        } catch (\Exception $exception) {
            return Helper::exceptionJSON($exception, 422, "Members");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Member\StoreMemberRequest $request
     * @return AccountResource
     */
    public function store(StoreMemberRequest $request)
    {
        DB::beginTransaction();
        try {
            // create contact
            $contact = ContactServices::createContact($request);
            // create account and other registration processes for customer only
            if ($request->type === "customer") {
                $account = AccountServices::createAccount($request, $contact);
                // if account creation is successful then only process with dispatch request
                if ($account->id) {
                    //                    ProcessAdditionalRegistrationSteps::dispatch(
                    //                        $request->all(),
                    //                        $account
                    //                    );
                    $subscription = SubscriptionServices::createSubscription(
                        $request,
                        $account
                    );

                    $bill = BillingServices::createUIBillingEntry(
                        $request,
                        $account->id
                    );

                    $transaction = TransactionServices::logUITransaction(
                        $request,
                        $account->id,
                        $bill->id
                    );
                    $accountWithContactDetails = Account::with("contact")->find(
                        $account->id
                    );

                    // send email on registration
                    if (
                        $subscription &&
                        $transaction &&
                        $accountWithContactDetails
                    ) {
                        Mail::to($request->email)
                            ->send(
                                new MemberRegistered($account->id)
                            );
                    }
                }
            }
            DB::commit();
            return new AccountResource($accountWithContactDetails);
        } catch (\Exception $exception) {
            DB::rollback();
            return Helper::exceptionJSON($exception, 422, "Member");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Member $member
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($id)
    {
        try {
            $members = ContactServices::customerByID($id);
            if ($members->count() === 0) {
                abort(404, "No records found for entered id " . $id);
            }
            return ContactResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "search");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Member\UpdateMemberRequest $request
     * @param Member $member
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Member $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        //
    }

    /**
     * Searches member by registration number (registration number is set as unique so we will get one member only)
     * @param $registrationNumber
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function searchByRegistration($registrationNumber)
    {
        $registrationNumber = trim($registrationNumber);
        $input = [
            "registration_number" => $registrationNumber,
        ];
        $rules = [
            "registration_number" => "required|string",
        ];

        Validator::make(
            $input,
            $rules,
            $messages = [
                "required" =>
                    "The :attribute field is required for searching members",
                "string" =>
                    "The :attribute field should be a registration number",
            ]
        )->validate();

        try {
            $member = AccountServices::customerByAccount($registrationNumber);
            if ($member->count() === 0) {
                abort(
                    404,
                    "There is no members found for entered account number " .
                        $registrationNumber
                );
            }
            return AccountResource::collection($member);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "search");
        }
    }

    /**
     * Search members by phone number (there can be multiple members with same phone number)
     * @param $phone_number
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function searchByPhone($phone_number)
    {
        $phone_number = trim($phone_number);
        $input = [
            "phone_number" => $phone_number,
        ];
        $rules = [
            "phone_number" => "required|integer|digits:10",
        ];

        Validator::make(
            $input,
            $rules,
            $messages = [
                "required" =>
                    "The :attribute field is required for searching members",
                "integer" =>
                    "The :attribute field should be a valid phone number.",
            ]
        )->validate();

        try {
            $members = ContactServices::customerByPhone($phone_number);
            if ($members->count() === 0) {
                abort(
                    404,
                    "There are no members found for entered phone number " .
                        $phone_number
                );
            }
            return ContactResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "search");
        }
    }

    /**
     * Search member by email (email is set as unique so we will get one member only)
     * @param $email
     * @return ContactResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function searchByEmail($email)
    {
        $email = trim($email);
        $input = [
            "email" => $email,
        ];
        $rules = [
            "email" => "required|email:rfc",
        ];

        Validator::make($input, $rules)->validate();

        try {
            // single member will be fetched as we are storing unique email address
            $member = ContactServices::customerByEmail($email);
            if ($member->count() === 0) {
                abort(
                    404,
                    "There is no member found for entered email address " .
                        $email
                );
            }
            return ContactResource::collection($member);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "search");
        }
    }

    /**
     * Get all active members
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function activeMembersList()
    {
        try {
            // single member will be fetched as we are storing unique email address
            $members = AccountServices::getActiveCustomers();
            if ($members->count() === 0) {
                abort(404, "There are no active members");
            }
            return AccountResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "index");
        }
    }

    /**
     * Get all active members with upcoming due date
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getActiveCustomersWithUpcomingDueDate()
    {
        try {
            $members = AccountServices::getActiveCustomersWithUpcomingDueDate();
            if ($members->count() === 0) {
                abort(404, "There are no members with outstanding payment");
            }
            return AccountResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "index");
        }
    }

    /**
     * Get all active members with over due date
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getActiveCustomersWithOverDueDate()
    {
        try {
            $members = AccountServices::getActiveCustomersWithOverDueDate();
            if ($members->count() === 0) {
                abort(404, "There are no members with over due payment date");
            }
            return AccountResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "index");
        }
    }

    /**
     * Get all active members
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function inactiveMembersList()
    {
        try {
            // single member will be fetched as we are storing unique email address
            $members = AccountServices::getInactiveCustomers();
            if ($members->count() === 0) {
                abort(404, "There are no inactive members");
            }
            return AccountResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "index");
        }
    }

    /**
     * Get all suspended members
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function suspendedCustomers()
    {
        try {
            // single member will be fetched as we are storing unique email address
            $members = AccountServices::getSuspendedCustomers();
            if ($members->count() === 0) {
                abort(404, "There are no suspended members");
            }
            return AccountResource::collection($members);
        } catch (Throwable $exception) {
            return Helper::exceptionJSON($exception, 404, "index");
        }
    }
}
