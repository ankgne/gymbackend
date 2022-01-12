<?php

namespace App\Services;

use App\Models\Member\Contact;
use Illuminate\Support\Facades\Auth;

class ContactServices
{
    public static function createContact($request)
    {
        // create contact

        $fullName =
            strtolower($request->first_name) .
            " " .
            strtolower($request->last_name);

        return Contact::create([
            "name" => $fullName,
            "type" => $request->type,
            "gender" => $request->gender,
            "dob" => $request->dob,
            "phone" => $request->phone_number,
            "email" => $request->email,
            "address" => $request->address,
            "city" => $request->city,
            "state" => $request->state,
            "pincode" => $request->pincode,
            "created_by" => Auth::id(),
        ]);
    }

    /***
     * Returns the contact based on phone number
     * @param $phoneNumber
     * @return mixed
     */
    public static function customerByPhone($phoneNumber)
    {
        if ($phoneNumber) {
            return Contact::with(["account.subscriptions", "account.bills"])
                ->customer()
                ->where("phone", $phoneNumber)
                ->get();
        }
        return null;
    }

    /***
     * Returns the contact based on email address
     * @param $email
     * @return mixed
     */
    public static function customerByEmail($email)
    {
        if ($email) {
            return Contact::with("account.subscriptions")
                ->customer()
                ->where("email", $email)
                ->get();
        }
        return null;
    }
}
