<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\UserResource;
use App\Jobs\ProcessAdditionalRegistrationSteps;
use App\Models\Member\Account;
use App\Models\Member\Contact;
use App\Models\Member\Member;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Models\User;
use App\Services\AccountServices;
use App\Services\ContactServices;
use App\Services\Helper;
use http\Message;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MemberController extends Controller
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
     * @param \App\Http\Requests\Member\StoreMemberRequest $request
     * @return ContactResource
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
                    ProcessAdditionalRegistrationSteps::dispatch(
                        $request->all(),
                        $account
                    );
                }
            }
            DB::commit();
            return new ContactResource($contact);
        } catch (\Exception $exception) {
            DB::rollback();
            return Helper::exceptionJSON($exception, 422, "Member");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Member\Member $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Member\UpdateMemberRequest $request
     * @param \App\Models\Member\Member $member
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Member\Member $member
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
            return Helper::exceptionJSON($exception,404,"search");
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
            return Helper::exceptionJSON($exception,404,"search");
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
            return Helper::exceptionJSON($exception,404,"search");
        }
    }
}
