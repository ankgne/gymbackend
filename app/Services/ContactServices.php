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
            return Contact::with([
                "account.subscriptions" => function ($query) {
                    $query->active()->latest();
                },
                "account.bills" => function ($query) {
                    $query->latest();
                },
            ])
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
            return Contact::with([
                "account.subscriptions" => function ($query) {
                    $query->active()->latest();
                },
                "account.bills" => function ($query) {
                    $query->latest();
                },
            ])
                ->customer()
                ->where("email", $email)
                ->get();
        }
        return null;
    }

    /***
     * Returns the contact based on email address
     * @param $id
     * @return mixed
     */
    public static function customerByID($id)
    {
        if ($id) {
            return Contact::with([
                "account.subscriptions" => function ($query) {
                    $query->active()->latest();
                },
                "account.bills" => function ($query) {
                    $query->latest();
                },
                "account.transactions" => function ($query) {
                    $query->latest()->first();
                },
            ])
                ->customer()
                ->where("id", $id)
                ->get();
        }
        return null;
    }

    /**
     * Returns the list of all customer
     */
    public static function getCustomers()
    {
        return Contact::with([
            "account",
            "account.subscriptions" => function ($query) {
                $query->active()->latest();
            },
        ])
            ->customer()
            ->get();
    }

    /**
     * Update contact
     */
    public static function updateContact($request, Contact $contact)
    {
        $fullName =
            strtolower($request->first_name) .
            " " .
            strtolower($request->last_name);

        $contact->fill([
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
        ]);

        $contact->save();

        return $contact;
    }
}
